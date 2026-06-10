<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View { return view('profile.edit', ['user' => auth()->user()]); }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->validate([
            'name'   => ['required','string','max:255'],
            'email'  => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'phone'  => ['nullable','string','max:20'],
            'avatar' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ],[
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan akun lain.',
            'avatar.max'     => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = $request->only('name','email','phone');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars','public');
        }

        $user->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
