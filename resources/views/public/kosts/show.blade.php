<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kost->name }} — MyKostApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .facility-tag { display:inline-block; background:#f1f5f9; color:#475569; font-size:12px; padding:4px 10px; border-radius:6px; }
        .room-card { border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; transition:box-shadow 0.2s; }
        .room-card:hover { box-shadow:0 8px 24px rgba(0,0,0,0.08); }
    </style>
</head>
<body style="background:#f8fafc; min-height:100vh;">

{{-- Navbar --}}
<nav style="background:#fff; border-bottom:1px solid #e2e8f0; position:sticky; top:0; z-index:50;">
    <div style="max-width:1100px; margin:0 auto; padding:0 24px; height:60px; display:flex; align-items:center; justify-content:space-between;">
        <a href="{{ route('public.kosts.index') }}"
           style="display:flex; align-items:center; gap:8px; text-decoration:none; color:#475569; font-size:14px; font-weight:500;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Kos
        </a>
        <div style="display:flex; align-items:center; gap:10px;">
            @auth
                @if(auth()->user()->isAdmin())
                <a href="{{ route('dashboard') }}" style="font-size:13px; color:#2563eb; text-decoration:none; font-weight:500;">Dashboard</a>
                @else
                <a href="{{ route('tenant.dashboard') }}" style="font-size:13px; color:#2563eb; text-decoration:none; font-weight:500;">Dashboard Saya</a>
                @endif
            @else
            <a href="{{ route('login') }}" style="font-size:13px; color:#475569; text-decoration:none;">Masuk</a>
            <a href="{{ route('register') }}"
               style="font-size:13px; font-weight:600; color:#fff; background:#2563eb; padding:8px 18px; border-radius:8px; text-decoration:none;">
                Daftar
            </a>
            @endauth
        </div>
    </div>
</nav>

<main style="max-width:1100px; margin:0 auto; padding:32px 24px;">

{{-- Foto Gallery --}}
@if($kost->photos && count($kost->photos) > 0)
<div style="border-radius:16px; overflow:hidden; height:360px; background:#e2e8f0; margin-bottom:24px; position:relative;">
    <img src="{{ $kost->first_photo }}" alt="{{ $kost->name }}"
         style="width:100%; height:100%; object-fit:cover;">
    @if(count($kost->photos) > 1)
    <div style="position:absolute; bottom:12px; right:12px; background:rgba(0,0,0,0.6); color:#fff; font-size:12px; padding:6px 12px; border-radius:8px; font-weight:600;">
        +{{ count($kost->photos) - 1 }} foto lagi
    </div>
    @endif
</div>
@else
<div style="border-radius:16px; height:240px; background:linear-gradient(135deg,#e0e7ff,#c7d2fe); display:flex; align-items:center; justify-content:center; margin-bottom:24px;">
    <div style="text-align:center;">
        <svg width="64" height="64" fill="none" stroke="#818cf8" viewBox="0 0 24 24" style="margin-bottom:8px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <p style="color:#6366f1; font-size:14px;">Belum ada foto</p>
    </div>
</div>
@endif

<div style="display:grid; grid-template-columns:1fr 300px; gap:24px; align-items:start;">

{{-- LEFT: Detail --}}
<div>
    {{-- Header --}}
    <div style="background:#fff; border-radius:16px; padding:24px; border:1px solid #e2e8f0; margin-bottom:20px;">
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <span style="background:#eff6ff; color:#1d4ed8; font-size:12px; font-weight:600; padding:4px 12px; border-radius:50px;">
                {{ $kost->type_label }}
            </span>
            @if($availableRooms->count() > 0)
            <span style="background:#f0fdf4; color:#15803d; font-size:12px; font-weight:600; padding:4px 12px; border-radius:50px;">
                ✓ {{ $availableRooms->count() }} Kamar Kosong
            </span>
            @else
            <span style="background:#fef2f2; color:#dc2626; font-size:12px; font-weight:600; padding:4px 12px; border-radius:50px;">
                Penuh
            </span>
            @endif
        </div>

        <h1 style="font-size:26px; font-weight:800; color:#1e293b; margin:0 0 8px;">{{ $kost->name }}</h1>

        @if($kost->address)
        <p style="font-size:14px; color:#64748b; display:flex; align-items:flex-start; gap:6px; margin:0 0 16px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:2px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $kost->address }}{{ $kost->city ? ', '.$kost->city : '' }}
        </p>
        @endif

        @if($kost->description)
        <p style="font-size:14px; color:#475569; line-height:1.7; padding-top:16px; border-top:1px solid #f1f5f9; margin:0;">
            {{ $kost->description }}
        </p>
        @endif
    </div>

    {{-- Kamar --}}
    <div style="background:#fff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden;">
        <div style="padding:20px 24px; border-bottom:1px solid #f1f5f9;">
            <h2 style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">
                Daftar Kamar <span style="color:#94a3b8; font-weight:400;">({{ $kost->rooms->count() }} kamar)</span>
            </h2>
        </div>

        @forelse($kost->rooms as $room)
        <div style="padding:20px 24px; border-bottom:1px solid #f8fafc; display:flex; gap:16px; align-items:flex-start;">

            {{-- Room photo --}}
            <div style="width:100px; height:80px; border-radius:10px; overflow:hidden; background:#f1f5f9; flex-shrink:0;">
                @if($room->first_photo)
                <img src="{{ $room->first_photo }}" alt="" style="width:100%; height:100%; object-fit:cover;">
                @else
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                    <svg width="28" height="28" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:4px;">
                    <span style="font-size:15px; font-weight:700; color:#1e293b;">Kamar {{ $room->number }}</span>
                    <span style="font-size:11px; font-weight:600; padding:2px 8px; border-radius:50px;
                        {{ $room->status === 'available' ? 'background:#f0fdf4; color:#15803d;' : ($room->status === 'maintenance' ? 'background:#fffbeb; color:#92400e;' : 'background:#fef2f2; color:#dc2626;') }}">
                        {{ $room->status_label }}
                    </span>
                </div>
                <p style="font-size:12px; color:#94a3b8; margin:0 0 8px;">Lantai {{ $room->floor }}</p>
                @if($room->facilities->count() > 0)
                <div style="display:flex; flex-wrap:wrap; gap:4px;">
                    @foreach($room->facilities->take(5) as $f)
                    <span class="facility-tag">{{ $f->name }}</span>
                    @endforeach
                    @if($room->facilities->count() > 5)
                    <span class="facility-tag">+{{ $room->facilities->count()-5 }} lagi</span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Price & CTA --}}
            <div style="text-align:right; flex-shrink:0; display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                <div>
                    <p style="font-size:17px; font-weight:800; color:#2563eb; margin:0;">
                        Rp {{ number_format($room->price, 0, ',', '.') }}
                    </p>
                    <p style="font-size:11px; color:#94a3b8; margin:2px 0 0;">/bulan</p>
                </div>
                @if($room->status === 'available')
                    @auth
                        @if(auth()->user()->isTenant())
                            <a href="{{ route('tenant.applications.create', ['room_id' => $room->id]) }}"
                               style="display:inline-block; font-size:12px; font-weight:600; color:#fff; background:#2563eb; padding:6px 12px; border-radius:6px; text-decoration:none; transition:background 0.2s;"
                               onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                                Ajukan Sewa
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           style="display:inline-block; font-size:12px; font-weight:600; color:#fff; background:#2563eb; padding:6px 12px; border-radius:6px; text-decoration:none; transition:background 0.2s;"
                           onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                            Ajukan Sewa
                        </a>
                    @endauth
                @endif
            </div>
        </div>
        @empty
        <div style="padding:48px; text-align:center; color:#94a3b8;">
            <p style="margin:0; font-size:14px;">Belum ada kamar terdaftar</p>
        </div>
        @endforelse
    </div>
</div>

{{-- RIGHT: Kontak & CTA --}}
<div style="position:sticky; top:80px;">
    <div style="background:#fff; border-radius:16px; padding:24px; border:1px solid #e2e8f0; margin-bottom:16px;">
        @if($kost->min_price)
        <div style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #f1f5f9;">
            <p style="font-size:12px; color:#94a3b8; margin:0 0 4px;">Harga mulai</p>
            <p style="font-size:28px; font-weight:800; color:#2563eb; margin:0; line-height:1.1;">
                Rp {{ number_format($kost->min_price, 0, ',', '.') }}
            </p>
            <p style="font-size:13px; color:#94a3b8; margin:4px 0 0;">/bulan</p>
        </div>
        @endif

        @if($kost->phone)
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kost->phone) }}?text=Halo, saya tertarik dengan kos {{ urlencode($kost->name) }}"
           target="_blank"
           style="display:block; background:#16a34a; color:#fff; font-weight:700; font-size:14px; text-decoration:none; padding:14px; border-radius:12px; text-align:center; margin-bottom:10px; transition:background 0.2s;"
           onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            💬 Hubungi via WhatsApp
        </a>
        @endif

        @if($kost->email)
        <a href="mailto:{{ $kost->email }}"
           style="display:block; background:#f8fafc; color:#475569; font-weight:600; font-size:14px; text-decoration:none; padding:12px; border-radius:12px; text-align:center; border:1px solid #e2e8f0; margin-bottom:10px;">
            ✉️ {{ $kost->email }}
        </a>
        @endif

        @guest
        <div style="background:#eff6ff; border-radius:12px; padding:16px; text-align:center; margin-top:8px;">
            <p style="font-size:13px; color:#1d4ed8; font-weight:500; margin:0 0 12px;">
                Daftar akun untuk proses sewa lebih mudah
            </p>
            <a href="{{ route('register') }}"
               style="display:block; background:#2563eb; color:#fff; font-weight:600; font-size:13px; text-decoration:none; padding:10px; border-radius:8px;">
                Daftar Sekarang
            </a>
        </div>
        @endguest
    </div>

    {{-- Info box --}}
    <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px;">
        <p style="font-size:13px; color:#92400e; margin:0; display:flex; gap:8px; align-items:flex-start;">
            <span>💡</span>
            <span>Hubungi langsung pemilik kos untuk konfirmasi ketersediaan dan proses sewa.</span>
        </p>
    </div>
</div>
</div>

</main>

{{-- Footer --}}
<footer style="background:#0f172a; padding:24px; text-align:center; margin-top:48px;">
    <p style="font-size:12px; color:#475569; margin:0;">
        © {{ date('Y') }} MyKostApp •
        <a href="{{ route('public.kosts.index') }}" style="color:#475569; text-decoration:none;">Cari Kos</a>
    </p>
</footer>

</body>
</html>
