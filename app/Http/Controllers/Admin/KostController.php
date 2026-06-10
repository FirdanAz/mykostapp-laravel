<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kost;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KostController extends Controller
{
    public function index(): View
    {
        $kost = Kost::first() ?? new Kost();
        return view('kosts.index', compact('kost'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'address'     => ['required','string'],
            'phone'       => ['nullable','string','max:20'],
            'email'       => ['nullable','email','max:255'],
            'website'     => ['nullable','url','max:255'],
            'photos'      => ['nullable','array'],
            'photos.*'    => ['image','mimes:jpg,jpeg,png,webp','max:3072'],
            'logo'        => ['nullable','image','mimes:jpg,jpeg,png,webp,svg','max:1024'],
        ]);

        $data  = $request->except('photos','logo');
        $kost  = Kost::first();

        if ($request->hasFile('photos')) {
            if ($kost && $kost->photos) {
                foreach ($kost->photos as $old) Storage::disk('public')->delete($old);
            }
            $paths = [];
            foreach ($request->file('photos') as $p) $paths[] = $p->store('kost','public');
            $data['photos'] = $paths;
        }

        if ($request->hasFile('logo')) {
            if ($kost && $kost->logo) Storage::disk('public')->delete($kost->logo);
            $data['logo'] = $request->file('logo')->store('kost','public');
        }

        Kost::updateOrCreate(['id' => $kost?->id], $data);

        return back()->with('success', 'Data kost berhasil disimpan.');
    }
}
