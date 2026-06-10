<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function showForgotForm(): View { return view('auth.forgot-password'); }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required','email']]);
        $status = Password::sendResetLink($request->only('email'));
        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
        }
        throw ValidationException::withMessages(['email' => [trans($status)]]);
    }

    public function showResetForm(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required','email'],
            'password' => ['required','confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            fn($user) => $user->forceFill(['password' => $request->password])->save()
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
        }
        throw ValidationException::withMessages(['email' => [trans($status)]]);
    }

    public function showChangeForm(): View { return view('profile.change-password'); }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required','confirmed', Rules\Password::defaults()],
        ],[
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            throw ValidationException::withMessages(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $request->user()->update(['password' => $request->password]);
        return back()->with('success', 'Password berhasil diubah.');
    }
}
