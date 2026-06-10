<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Kost;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user ────────────────────────────────────────────
        $admin = User::create([
            'name'              => 'Admin MyKost',
            'email'             => 'admin@mykost.com',
            'password'          => Hash::make('password123'),
            'role'              => 'admin',
            'phone'             => '081234567890',
            'email_verified_at' => now(),
        ]);

        // ── Kost data ─────────────────────────────────────────────
        $kost = Kost::create([
            'name'        => 'Kost Melati Indah',
            'description' => 'Kost nyaman dan bersih di pusat kota, cocok untuk mahasiswa dan karyawan. Dilengkapi fasilitas lengkap.',
            'address'     => 'Jl. Melati No. 12, Semarang, Jawa Tengah 50131',
            'phone'       => '081234567890',
            'email'       => 'info@kostmelati.com',
        ]);

        // ── Facilities ────────────────────────────────────────────
        $facilities = [
            ['name' => 'WiFi',          'icon' => 'wifi'],
            ['name' => 'AC',            'icon' => 'air-conditioning'],
            ['name' => 'Kamar Mandi Dalam', 'icon' => 'bath'],
            ['name' => 'Lemari',        'icon' => 'package'],
            ['name' => 'Kasur',         'icon' => 'bed'],
            ['name' => 'Meja Belajar',  'icon' => 'desk'],
            ['name' => 'Parkir Motor',  'icon' => 'motorbike'],
            ['name' => 'Parkir Mobil',  'icon' => 'car'],
            ['name' => 'Dapur Bersama', 'icon' => 'tools-kitchen'],
            ['name' => 'TV',            'icon' => 'device-tv'],
        ];
        foreach ($facilities as $f) {
            Facility::create($f);
        }

        // ── Rooms ─────────────────────────────────────────────────
        $roomData = [
            ['number'=>'101','floor'=>1,'price'=>800000, 'status'=>'occupied'],
            ['number'=>'102','floor'=>1,'price'=>800000, 'status'=>'available'],
            ['number'=>'103','floor'=>1,'price'=>900000, 'status'=>'available'],
            ['number'=>'201','floor'=>2,'price'=>1000000,'status'=>'occupied'],
            ['number'=>'202','floor'=>2,'price'=>1000000,'status'=>'maintenance'],
            ['number'=>'203','floor'=>2,'price'=>1100000,'status'=>'occupied'],
            ['number'=>'301','floor'=>3,'price'=>1200000,'status'=>'available'],
            ['number'=>'302','floor'=>3,'price'=>1200000,'status'=>'occupied'],
        ];

        foreach ($roomData as $r) {
            $room = Room::create(array_merge($r, [
                'kost_id'     => $kost->id,
                'description' => 'Kamar '.$r['number'].' lantai '.$r['floor'].', luas 3x4 meter.',
            ]));
            $facilityIds = Facility::inRandomOrder()->take(rand(3,6))->pluck('id');
            $room->facilities()->attach($facilityIds);
        }

        // ── Tenants for occupied rooms ────────────────────────────
        $occupiedRooms = Room::where('status','occupied')->get();
        $tenantNames   = ['Budi Santoso','Siti Rahayu','Ahmad Fauzi','Dewi Lestari'];
        foreach ($occupiedRooms as $i => $room) {
            Tenant::create([
                'room_id'    => $room->id,
                'name'       => $tenantNames[$i] ?? 'Penghuni '.$room->number,
                'email'      => 'tenant'.($i+1).'@example.com',
                'phone'      => '0812000'.(1000+$i),
                'gender'     => $i % 2 === 0 ? 'male' : 'female',
                'address'    => 'Jl. Contoh No. '.($i+1).', Semarang',
                'start_date' => now()->subMonths(rand(1,6)),
                'status'     => 'active',
            ]);
        }

        // ── Settings ──────────────────────────────────────────────
        $settings = [
            'app_name'          => 'MyKostApp',
            'app_currency'      => 'IDR',
            'invoice_due_days'  => '10',
            'whatsapp_number'   => '081234567890',
            'late_fee'          => '50000',
        ];
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
