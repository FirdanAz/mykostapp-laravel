<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalApplication;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\Invoice;
use App\Models\Setting;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RentalApplicationController extends Controller
{
    private function getKost()
    {
        $kost = auth()->user()->kost;
        if (!$kost) {
            abort(403, 'Setup kos Anda terlebih dahulu.');
        }
        return $kost;
    }

    public function index(Request $request): View
    {
        $kost = $this->getKost();

        $query = RentalApplication::whereHas('room', fn($q) => $q->where('kost_id', $kost->id))
            ->with(['room', 'user'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('admin.applications.index', compact('applications', 'kost'));
    }

    public function show(RentalApplication $application): View
    {
        $kost = $this->getKost();

        if ($application->room->kost_id !== $kost->id) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan sewa ini.');
        }

        $application->load(['room', 'user']);

        return view('admin.applications.show', compact('application', 'kost'));
    }

    public function approve(RentalApplication $application): RedirectResponse
    {
        $kost = $this->getKost();

        if ($application->room->kost_id !== $kost->id) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan sewa ini.');
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $room = $application->room;
        if ($room->status !== 'available') {
            return back()->with('error', 'Kamar sudah tidak tersedia (terisi atau maintenance).');
        }

        DB::transaction(function () use ($application, $room) {
            // 1. Update Room status
            $room->update(['status' => 'occupied']);

            // 2. Create Tenant profile
            $tenant = Tenant::create([
                'user_id'    => $application->user_id,
                'room_id'    => $application->room_id,
                'name'       => $application->name,
                'email'      => $application->email,
                'phone'      => $application->phone,
                'gender'     => $application->gender,
                'address'    => $application->address,
                'id_card'    => $application->id_card, // NIK
                'photo'      => null, // Biarkan foto profil tenant kosong/avatar dulu
                'start_date' => $application->start_date,
                'end_date'   => Carbon::parse($application->start_date)->addMonths($application->duration_months),
                'status'     => 'active',
            ]);

            // 3. Update application status
            $application->update(['status' => 'approved']);

            // 4. Generate first month invoice
            $dueDays = (int) Setting::get('invoice_due_days', 10);
            $invoice = Invoice::create([
                'tenant_id'      => $tenant->id,
                'invoice_number' => Invoice::generateNumber(),
                'amount'         => $room->price,
                'due_date'       => Carbon::parse($application->start_date)->addDays($dueDays),
                'period_start'   => $application->start_date,
                'period_end'     => Carbon::parse($application->start_date)->addMonth()->subDay(),
                'status'         => 'unpaid',
                'notes'          => 'Tagihan Bulan Pertama (Persetujuan Pengajuan Sewa)',
            ]);

            // 5. Send notification to Tenant
            NotificationService::send(
                $application->user,
                'rental_application_approved',
                'Pengajuan Sewa Disetujui',
                "Selamat! Pengajuan sewa Kamar {$room->number} di {$room->kost->name} telah disetujui. Silakan bayar tagihan pertama Anda.",
                ['invoice_id' => $invoice->id],
                route('tenant.invoices.show', $invoice)
            );
        });

        return redirect()->route('admin.applications.index')
            ->with('success', "Pengajuan sewa {$application->name} berhasil disetujui. Kamar kini terisi dan tagihan pertama telah dikirim.");
    }

    public function reject(Request $request, RentalApplication $application): RedirectResponse
    {
        $kost = $this->getKost();

        if ($application->room->kost_id !== $kost->id) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan sewa ini.');
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $application->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Send notification to Tenant
        NotificationService::send(
            $application->user,
            'rental_application_rejected',
            'Pengajuan Sewa Ditolak',
            "Maaf, pengajuan sewa Kamar {$application->room->number} ditolak. Alasan: {$request->rejection_reason}",
            [],
            route('tenant.applications.index')
        );

        return redirect()->route('admin.applications.index')
            ->with('success', "Pengajuan sewa {$application->name} berhasil ditolak.");
    }
}
