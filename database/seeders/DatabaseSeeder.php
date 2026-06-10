<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo Admin ────────────────────────────────────────────────
        User::create([
            'name'              => 'Admin Demo',
            'email'             => 'admin@demo.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'phone'             => '081234567890',
            'email_verified_at' => now(),
        ]);

        // ── Demo Tenant (akun tanpa profil tenant dulu) ───────────────
        User::create([
            'name'              => 'Tenant Demo',
            'email'             => 'tenant@demo.com',
            'password'          => Hash::make('password'),
            'role'              => 'tenant',
            'phone'             => '089876543210',
            'email_verified_at' => now(),
        ]);

        // ── Master Facilities ─────────────────────────────────────────
        $facilities = [
            ['name' => 'WiFi',              'icon' => 'wifi'],
            ['name' => 'AC',                'icon' => 'air-conditioning'],
            ['name' => 'Kamar Mandi Dalam', 'icon' => 'bath'],
            ['name' => 'Lemari',            'icon' => 'package'],
            ['name' => 'Kasur',             'icon' => 'bed'],
            ['name' => 'Meja Belajar',      'icon' => 'desk'],
            ['name' => 'Parkir Motor',      'icon' => 'motorbike'],
            ['name' => 'Parkir Mobil',      'icon' => 'car'],
            ['name' => 'Dapur Bersama',     'icon' => 'tools-kitchen'],
            ['name' => 'TV',                'icon' => 'device-tv'],
            ['name' => 'Laundry',           'icon' => 'wash'],
            ['name' => 'CCTV',              'icon' => 'camera'],
        ];
        foreach ($facilities as $f) {
            Facility::create($f);
        }

        // ── App Settings ───────────────────────────────────────────────
        $settings = [
            'app_name'         => 'MyKostApp',
            'app_currency'     => 'IDR',
            'invoice_due_days' => '10',
            'late_fee'         => '50000',
        ];
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
