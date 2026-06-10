<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── Carbon locale (Indonesia) ──────────────────────
        Carbon::setLocale('id');

        // ── Tailwind pagination ─────────────────────────────
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');

        // ── Force HTTPS in production ───────────────────────
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // ── Blade directives ────────────────────────────────

        // @money(1000000) → Rp 1.000.000
        Blade::directive('money', function ($amount) {
            return "<?php echo 'Rp '.number_format($amount, 0, ',', '.'); ?>";
        });

        // @date($date) → 1 Januari 2025
        Blade::directive('date', function ($date) {
            return "<?php echo \\Carbon\\Carbon::parse($date)->translatedFormat('d F Y'); ?>";
        });

        // @datetime($date) → 1 Januari 2025 14:30
        Blade::directive('datetime', function ($date) {
            return "<?php echo \\Carbon\\Carbon::parse($date)->translatedFormat('d F Y H:i'); ?>";
        });

        // @statusbadge($status, $label)
        Blade::directive('statusbadge', function ($expression) {
            [$status, $label] = explode(',', $expression, 2);
            return "<?php
                \$colors = ['unpaid'=>'bg-amber-100 text-amber-700','paid'=>'bg-green-100 text-green-700','overdue'=>'bg-orange-100 text-orange-700','pending_verification'=>'bg-blue-100 text-blue-700','rejected'=>'bg-red-100 text-red-700','new'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-amber-100 text-amber-700','resolved'=>'bg-green-100 text-green-700'];
                \$class = \$colors[trim($status)] ?? 'bg-slate-100 text-slate-600';
                echo '<span class=\"text-xs font-medium px-2.5 py-1 rounded-full '.\$class.'\">' . trim($label) . '</span>';
            ?>";
        });
    }
}
