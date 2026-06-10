<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\RentalApplication;
use App\Models\Room;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalApplicationController extends Controller
{
    public function index(): View
    {
        $applications = auth()->user()->rentalApplications()
            ->with(['room.kost'])
            ->latest()
            ->paginate(10);

        return view('tenant.applications.index', compact('applications'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $roomId = $request->query('room_id');
        if (!$roomId) {
            return redirect()->route('public.kosts.index')
                ->with('error', 'Silakan pilih kamar yang ingin disewa terlebih dahulu.');
        }

        $room = Room::with('kost')->findOrFail($roomId);

        if ($room->status !== 'available') {
            return redirect()->route('public.kosts.show', $room->kost_id)
                ->with('error', 'Kamar ini sedang tidak tersedia untuk disewa.');
        }

        // Cek apakah user sudah punya pengajuan pending untuk kamar ini
        $existsPending = RentalApplication::where('user_id', auth()->id())
            ->where('room_id', $room->id)
            ->where('status', 'pending')
            ->exists();

        if ($existsPending) {
            return redirect()->route('tenant.applications.index')
                ->with('info', 'Anda sudah mengajukan sewa untuk kamar ini. Silakan tunggu proses verifikasi pemilik kos.');
        }

        $user = auth()->user();

        return view('tenant.applications.create', compact('room', 'user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'room_id'         => ['required', 'exists:rooms,id'],
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255'],
            'phone'           => ['required', 'string', 'max:20'],
            'gender'          => ['required', 'in:male,female'],
            'address'         => ['required', 'string', 'max:1000'],
            'id_card'         => ['required', 'string', 'max:50'],
            'id_card_photo'   => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'start_date'      => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'in:1,3,6,12'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ], [
            'id_card_photo.required' => 'Foto KTP wajib diunggah.',
            'id_card_photo.image'    => 'Berkas harus berupa gambar.',
            'id_card_photo.max'      => 'Ukuran foto KTP maksimal 2MB.',
        ]);

        $room = Room::with('kost.owner')->findOrFail($request->room_id);

        if ($room->status !== 'available') {
            return back()->with('error', 'Kamar ini tidak tersedia untuk disewa.');
        }

        $data = $request->only([
            'room_id', 'name', 'email', 'phone', 'gender',
            'address', 'id_card', 'start_date', 'duration_months', 'notes'
        ]);

        $data['user_id'] = auth()->id();
        $data['id_card_photo'] = $request->file('id_card_photo')->store('rental_applications', 'public');
        $data['status'] = 'pending';

        $application = RentalApplication::create($data);

        // Kirim notifikasi ke pemilik kos
        NotificationService::send(
            $room->kost->owner,
            'rental_application',
            'Pengajuan Sewa Baru',
            "Pencari kos {$application->name} telah mengajukan sewa untuk Kamar {$room->number}.",
            ['application_id' => $application->id],
            route('admin.applications.show', $application)
        );

        return redirect()->route('tenant.applications.index')
            ->with('success', 'Pengajuan sewa berhasil dikirim. Silakan tunggu konfirmasi pemilik kos.');
    }
}
