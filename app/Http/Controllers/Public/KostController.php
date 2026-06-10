<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Kost;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KostController extends Controller
{
    /** Halaman browse semua kos yang dipublish */
    public function index(Request $request): View
    {
        $query = Kost::where('is_published', true)
            ->with(['rooms'])
            ->withCount(['rooms', 'rooms as available_count' => fn($q) => $q->where('status', 'available')]);

        if ($request->search) {
            $query->where(fn($q) => $q
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%')
                ->orWhere('city', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        $kosts = $query->paginate(12)->withQueryString();

        return view('public.kosts.index', compact('kosts'));
    }

    /** Halaman detail kos + daftar kamar */
    public function show(Kost $kost): View
    {
        if (!$kost->is_published) {
            abort(404);
        }

        $kost->load(['rooms.facilities']);
        $availableRooms = $kost->rooms->where('status', 'available');
        $occupiedRooms  = $kost->rooms->where('status', 'occupied');

        return view('public.kosts.show', compact('kost', 'availableRooms', 'occupiedRooms'));
    }
}
