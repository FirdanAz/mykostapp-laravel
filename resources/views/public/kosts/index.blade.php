<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kos — MyKostApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .search-input {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            outline: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 14px;
            width: 100%;
            transition: background 0.2s;
        }
        .search-input::placeholder { color: rgba(255,255,255,0.6); }
        .search-input:focus { background: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.5); }
        .search-select {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            outline: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 14px;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 20px;
            padding-right: 40px;
        }
        .search-select option { color: #1e293b; background: #fff; }
        .search-select:focus { background-color: rgba(255,255,255,0.25); }
        .kos-card:hover { box-shadow: 0 20px 40px rgba(0,0,0,0.12); transform: translateY(-3px); }
        .kos-card { transition: all 0.25s ease; }
    </style>
</head>
<body style="background:#f8fafc; min-height:100vh;">

{{-- ═══ NAVBAR ═══════════════════════════════════════════════════ --}}
<nav style="background:#fff; border-bottom:1px solid #e2e8f0; position:sticky; top:0; z-index:50;">
    <div style="max-width:1200px; margin:0 auto; padding:0 24px; height:64px; display:flex; align-items:center; justify-content:space-between;">
        <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
            <div style="width:36px; height:36px; background:#2563eb; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <span style="font-weight:700; font-size:16px; color:#1e293b;">MyKostApp</span>
        </a>

        <div style="display:flex; align-items:center; gap:12px;">
            @auth
                @if(auth()->user()->isAdmin())
                <a href="{{ route('dashboard') }}"
                   style="font-size:14px; font-weight:500; color:#475569; text-decoration:none; padding:8px 16px; border-radius:8px; transition:background 0.15s;"
                   onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                    Dashboard Admin
                </a>
                @else
                <a href="{{ route('tenant.dashboard') }}"
                   style="font-size:14px; font-weight:500; color:#475569; text-decoration:none; padding:8px 16px; border-radius:8px; transition:background 0.15s;">
                    Dashboard Saya
                </a>
                @endif
            @else
            <a href="{{ route('login') }}"
               style="font-size:14px; font-weight:500; color:#475569; text-decoration:none; padding:8px 16px; border-radius:8px;">
                Masuk
            </a>
            <a href="{{ route('register') }}"
               style="font-size:14px; font-weight:600; color:#fff; background:#2563eb; text-decoration:none; padding:9px 20px; border-radius:10px; transition:background 0.2s;"
               onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                Daftar Gratis
            </a>
            @endauth
        </div>
    </div>
</nav>

{{-- ═══ HERO ══════════════════════════════════════════════════════ --}}
<section style="background:linear-gradient(135deg, #1d4ed8 0%, #2563eb 40%, #4f46e5 100%); padding:64px 24px;">
    <div style="max-width:800px; margin:0 auto; text-align:center;">
        <div style="display:inline-block; background:rgba(255,255,255,0.15); border-radius:50px; padding:6px 16px; font-size:13px; color:rgba(255,255,255,0.9); margin-bottom:16px;">
            🏠 Platform Manajemen Kos Terpercaya
        </div>
        <h1 style="font-size:clamp(28px,5vw,48px); font-weight:800; color:#fff; line-height:1.2; margin:0 0 12px;">
            Temukan Kos Impianmu
        </h1>
        <p style="font-size:16px; color:rgba(255,255,255,0.75); margin:0 0 36px;">
            Cari dan bandingkan kos sesuai budget dan kebutuhanmu
        </p>

        {{-- Search Box --}}
        <form method="GET" action="{{ route('public.kosts.index') }}"
              style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); border-radius:16px; padding:8px; display:flex; gap:8px; max-width:640px; margin:0 auto; flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="search-input"
                   style="flex:1; min-width:180px;"
                   placeholder="Cari nama kos atau kota...">
            <select name="type" class="search-select" style="min-width:130px;">
                <option value="">Semua Tipe</option>
                <option value="campur" {{ request('type') == 'campur' ? 'selected' : '' }}>Campur</option>
                <option value="putra"  {{ request('type') == 'putra'  ? 'selected' : '' }}>Putra</option>
                <option value="putri"  {{ request('type') == 'putri'  ? 'selected' : '' }}>Putri</option>
            </select>
            <button type="submit"
                    style="background:#fff; color:#2563eb; font-weight:700; font-size:14px; border:none; padding:10px 24px; border-radius:10px; cursor:pointer; white-space:nowrap; transition:all 0.2s;"
                    onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='#fff'">
                🔍 Cari Kos
            </button>
        </form>

        {{-- Stats --}}
        <div style="display:flex; justify-content:center; gap:32px; margin-top:32px; flex-wrap:wrap;">
            <div style="text-align:center;">
                <p style="font-size:24px; font-weight:800; color:#fff; margin:0;">{{ \App\Models\Kost::where('is_published',true)->count() }}</p>
                <p style="font-size:12px; color:rgba(255,255,255,0.65); margin:2px 0 0;">Kos Terdaftar</p>
            </div>
            <div style="width:1px; background:rgba(255,255,255,0.2);"></div>
            <div style="text-align:center;">
                <p style="font-size:24px; font-weight:800; color:#fff; margin:0;">{{ \App\Models\Room::where('status','available')->count() }}</p>
                <p style="font-size:12px; color:rgba(255,255,255,0.65); margin:2px 0 0;">Kamar Tersedia</p>
            </div>
            <div style="width:1px; background:rgba(255,255,255,0.2);"></div>
            <div style="text-align:center;">
                <p style="font-size:24px; font-weight:800; color:#fff; margin:0;">{{ \App\Models\Tenant::where('status','active')->count() }}</p>
                <p style="font-size:12px; color:rgba(255,255,255,0.65); margin:2px 0 0;">Penghuni Aktif</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══ RESULTS ════════════════════════════════════════════════════ --}}
<main style="max-width:1200px; margin:0 auto; padding:40px 24px;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 style="font-size:18px; font-weight:700; color:#1e293b; margin:0 0 4px;">
                Daftar Kos
                @if(request('search')) — "<span style="color:#2563eb;">{{ request('search') }}</span>" @endif
            </h2>
            <p style="font-size:13px; color:#94a3b8; margin:0;">Menampilkan {{ $kosts->total() }} kos</p>
        </div>
        @if(request('search') || request('type'))
        <a href="{{ route('public.kosts.index') }}"
           style="font-size:13px; color:#2563eb; text-decoration:none; background:#eff6ff; padding:6px 14px; border-radius:8px;">
            ✕ Reset Filter
        </a>
        @endif
    </div>

    @if($kosts->count() > 0)
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:24px;">
        @foreach($kosts as $kost)
        <a href="{{ route('public.kosts.show', $kost) }}"
           class="kos-card"
           style="background:#fff; border-radius:16px; overflow:hidden; border:1px solid #e2e8f0; text-decoration:none; display:block;">

            {{-- Photo --}}
            <div style="height:200px; background:linear-gradient(135deg,#e2e8f0,#cbd5e1); position:relative; overflow:hidden;">
                @if($kost->first_photo)
                <img src="{{ $kost->first_photo }}" alt="{{ $kost->name }}"
                     style="width:100%; height:100%; object-fit:cover;">
                @else
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:8px;">
                    <svg width="48" height="48" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span style="font-size:12px; color:#94a3b8;">Belum ada foto</span>
                </div>
                @endif

                {{-- Badges --}}
                <div style="position:absolute; top:12px; left:12px;">
                    <span style="background:rgba(255,255,255,0.92); color:#374151; font-size:11px; font-weight:600; padding:3px 10px; border-radius:50px;">
                        {{ $kost->type_label }}
                    </span>
                </div>
                <div style="position:absolute; top:12px; right:12px;">
                    @if($kost->available_count > 0)
                    <span style="background:rgba(22,163,74,0.9); color:#fff; font-size:11px; font-weight:700; padding:3px 10px; border-radius:50px;">
                        {{ $kost->available_count }} kosong
                    </span>
                    @else
                    <span style="background:rgba(239,68,68,0.9); color:#fff; font-size:11px; font-weight:700; padding:3px 10px; border-radius:50px;">Penuh</span>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div style="padding:16px;">
                <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ $kost->name }}
                </h3>
                @if($kost->city || $kost->address)
                <p style="font-size:12px; color:#94a3b8; margin:0 0 12px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    📍 {{ $kost->city ?? Str::limit($kost->address, 40) }}
                </p>
                @endif

                <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid #f1f5f9;">
                    <div>
                        <p style="font-size:11px; color:#94a3b8; margin:0;">Mulai dari</p>
                        @if($kost->min_price)
                        <p style="font-size:15px; font-weight:700; color:#2563eb; margin:2px 0 0;">
                            Rp {{ number_format($kost->min_price, 0, ',', '.') }}<span style="font-size:11px; font-weight:400; color:#94a3b8;">/bln</span>
                        </p>
                        @else
                        <p style="font-size:13px; color:#94a3b8; margin:2px 0 0;">Hubungi pemilik</p>
                        @endif
                    </div>
                    <span style="font-size:12px; color:#94a3b8; background:#f8fafc; padding:4px 10px; border-radius:6px;">
                        {{ $kost->rooms_count }} kamar
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div style="margin-top:32px;">{{ $kosts->withQueryString()->links() }}</div>

    @else
    {{-- Empty State --}}
    <div style="text-align:center; padding:80px 24px;">
        <div style="width:100px; height:100px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px;">
            <svg width="48" height="48" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        @if(request('search') || request('type'))
        <h3 style="font-size:18px; font-weight:700; color:#475569; margin:0 0 8px;">Kos tidak ditemukan</h3>
        <p style="font-size:14px; color:#94a3b8; margin:0 0 20px;">Coba ubah kata kunci atau filter pencarian</p>
        <a href="{{ route('public.kosts.index') }}"
           style="display:inline-block; background:#2563eb; color:#fff; font-weight:600; font-size:14px; padding:10px 24px; border-radius:10px; text-decoration:none;">
            Lihat Semua Kos
        </a>
        @else
        <h3 style="font-size:18px; font-weight:700; color:#475569; margin:0 0 8px;">Belum Ada Kos Terdaftar</h3>
        <p style="font-size:14px; color:#94a3b8; margin:0 0 20px; max-width:400px; margin-left:auto; margin-right:auto;">
            Jadilah yang pertama mendaftarkan kos Anda di platform kami!
        </p>
        <a href="{{ route('register') }}"
           style="display:inline-block; background:#2563eb; color:#fff; font-weight:600; font-size:14px; padding:12px 28px; border-radius:12px; text-decoration:none;">
            Daftarkan Kos Saya →
        </a>
        @endif
    </div>
    @endif
</main>

{{-- ═══ CTA FOOTER ══════════════════════════════════════════════════ --}}
<section style="background:#0f172a; color:#fff; padding:64px 24px; margin-top:40px;">
    <div style="max-width:700px; margin:0 auto; text-align:center;">
        <h2 style="font-size:28px; font-weight:800; margin:0 0 10px;">Punya Kos? Kelola Lebih Mudah!</h2>
        <p style="font-size:15px; color:#94a3b8; margin:0 0 32px;">
            Satu platform untuk kelola penghuni, tagihan otomatis, dan laporan keuangan kos Anda
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="{{ route('register') }}"
               style="background:#2563eb; color:#fff; font-weight:700; font-size:15px; padding:14px 32px; border-radius:12px; text-decoration:none; transition:background 0.2s;"
               onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                🏠 Daftar sebagai Pemilik Kos
            </a>
            <a href="{{ route('login') }}"
               style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); color:#fff; font-weight:600; font-size:15px; padding:14px 32px; border-radius:12px; text-decoration:none;"
               onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                Sudah punya akun? Masuk
            </a>
        </div>
    </div>
</section>

<footer style="background:#0f172a; border-top:1px solid rgba(255,255,255,0.05); padding:20px 24px; text-align:center;">
    <p style="font-size:12px; color:#475569; margin:0;">© {{ date('Y') }} MyKostApp. Semua hak dilindungi.</p>
</footer>

</body>
</html>
