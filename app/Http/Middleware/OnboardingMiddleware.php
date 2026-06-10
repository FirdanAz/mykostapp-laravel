<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnboardingMiddleware
{
    /**
     * Admin yang belum setup kos diarahkan ke halaman onboarding.
     * Exempt: halaman onboarding itu sendiri, logout, dan profile.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Hanya berlaku untuk admin
        if ($user && $user->isAdmin()) {
            // Cek apakah admin sudah punya kos
            if (!$user->kost) {
                // Route yang diizinkan meski belum onboarding
                $exemptRoutes = [
                    'admin.kost.setup',
                    'admin.kost.setup.store',
                    'logout',
                    'profile.edit',
                    'profile.update',
                    'profile.password',
                    'profile.password.update',
                ];

                if (!in_array($request->route()?->getName(), $exemptRoutes)) {
                    return redirect()->route('admin.kost.setup')
                        ->with('info', 'Selamat datang! Silakan lengkapi data kos Anda terlebih dahulu.');
                }
            }
        }

        return $next($request);
    }
}
