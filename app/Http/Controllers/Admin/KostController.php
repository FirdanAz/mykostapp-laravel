<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KostController extends Controller
{
    /** Halaman setup kos (onboarding untuk admin baru) */
    public function setup(): View|RedirectResponse
    {
        if (auth()->user()->kost) {
            return redirect()->route('admin.kost.index')
                ->with('info', 'Kos Anda sudah tersimpan. Anda bisa mengeditnya di sini.');
        }
        return view('admin.onboarding.setup');
    }

    /** Simpan data kos saat onboarding */
    public function doSetup(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address'     => ['required', 'string'],
            'city'        => ['nullable', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'email'       => ['nullable', 'email', 'max:255'],
            'type'        => ['required', 'in:putra,putri,campur'],
            'photos'      => ['nullable', 'array'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:1024'],
        ], [
            'name.required'    => 'Nama kos wajib diisi.',
            'address.required' => 'Alamat kos wajib diisi.',
            'type.required'    => 'Tipe kos wajib dipilih.',
        ]);

        $data = $request->except('photos', 'logo');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $p) {
                $paths[] = $p->store('kost', 'public');
            }
            $data['photos'] = $paths;
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('kost', 'public');
        }

        $data['is_published'] = true;

        Kost::create($data);

        return redirect()->route('dashboard')
            ->with('success', 'Kos berhasil disimpan! Selamat datang di dashboard Anda.');
    }

    /** Halaman edit data kos milik admin ini */
    public function index(): View
    {
        $kost = auth()->user()->kost ?? new Kost();
        return view('admin.kost.index', compact('kost'));
    }

    /** Update data kos */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address'     => ['required', 'string'],
            'city'        => ['nullable', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'email'       => ['nullable', 'email', 'max:255'],
            'website'     => ['nullable', 'url', 'max:255'],
            'type'        => ['required', 'in:putra,putri,campur'],
            'photos'      => ['nullable', 'array'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:1024'],
            'is_published' => ['boolean'],
        ]);

        $kost = auth()->user()->kost;
        $data = $request->except('photos', 'logo');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('photos')) {
            if ($kost && $kost->photos) {
                foreach ($kost->photos as $old) Storage::disk('public')->delete($old);
            }
            $paths = [];
            foreach ($request->file('photos') as $p) {
                $paths[] = $p->store('kost', 'public');
            }
            $data['photos'] = $paths;
        }

        if ($request->hasFile('logo')) {
            if ($kost && $kost->logo) Storage::disk('public')->delete($kost->logo);
            $data['logo'] = $request->file('logo')->store('kost', 'public');
        }

        $data['is_published'] = $request->boolean('is_published');

        if ($kost) {
            $kost->update($data);
        } else {
            Kost::create($data);
        }

        return back()->with('success', 'Data kos berhasil disimpan.');
    }
}
