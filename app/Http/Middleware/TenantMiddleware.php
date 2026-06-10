<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isTenant()) {
            return redirect()->route('dashboard')
                ->with('error', 'Halaman ini hanya untuk penyewa kos.');
        }

        return $next($request);
    }
}
