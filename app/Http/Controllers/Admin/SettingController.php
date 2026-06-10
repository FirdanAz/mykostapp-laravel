<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'app_name'         => Setting::get('app_name','MyKostApp'),
            'invoice_due_days' => Setting::get('invoice_due_days','10'),
            'whatsapp_number'  => Setting::get('whatsapp_number',''),
            'late_fee'         => Setting::get('late_fee','0'),
            'app_currency'     => Setting::get('app_currency','IDR'),
        ];
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'app_name'         => ['required','string','max:100'],
            'invoice_due_days' => ['required','integer','min:1','max:30'],
            'whatsapp_number'  => ['nullable','string','max:20'],
            'late_fee'         => ['nullable','numeric','min:0'],
            'app_currency'     => ['required','string','max:10'],
        ]);

        foreach ($request->only('app_name','invoice_due_days','whatsapp_number','late_fee','app_currency') as $k => $v) {
            Setting::set($k, $v);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
