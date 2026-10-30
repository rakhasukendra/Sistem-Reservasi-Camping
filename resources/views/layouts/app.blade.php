<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rimba Sewa') | Rimba Sewa</title>
    <link rel="preload" href="{{ asset('fonts/dm-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/manrope-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face{font-family:'DM Sans';font-style:normal;font-weight:400;font-display:swap;src:url('{{ asset('fonts/dm-sans-latin-ext.woff2') }}') format('woff2');unicode-range:U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF}
        @font-face{font-family:'DM Sans';font-style:normal;font-weight:400;font-display:swap;src:url('{{ asset('fonts/dm-sans-latin.woff2') }}') format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD}
        @font-face{font-family:'DM Sans';font-style:normal;font-weight:500;font-display:swap;src:url('{{ asset('fonts/dm-sans-latin-ext.woff2') }}') format('woff2');unicode-range:U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF}
        @font-face{font-family:'DM Sans';font-style:normal;font-weight:500;font-display:swap;src:url('{{ asset('fonts/dm-sans-latin.woff2') }}') format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD}
        @font-face{font-family:'DM Sans';font-style:normal;font-weight:600;font-display:swap;src:url('{{ asset('fonts/dm-sans-latin-ext.woff2') }}') format('woff2');unicode-range:U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF}
        @font-face{font-family:'DM Sans';font-style:normal;font-weight:600;font-display:swap;src:url('{{ asset('fonts/dm-sans-latin.woff2') }}') format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD}
        @font-face{font-family:'DM Sans';font-style:normal;font-weight:700;font-display:swap;src:url('{{ asset('fonts/dm-sans-latin-ext.woff2') }}') format('woff2');unicode-range:U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF}
        @font-face{font-family:'DM Sans';font-style:normal;font-weight:700;font-display:swap;src:url('{{ asset('fonts/dm-sans-latin.woff2') }}') format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD}
        @font-face{font-family:'Manrope';font-style:normal;font-weight:500;font-display:swap;src:url('{{ asset('fonts/manrope-latin-ext.woff2') }}') format('woff2');unicode-range:U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF}
        @font-face{font-family:'Manrope';font-style:normal;font-weight:500;font-display:swap;src:url('{{ asset('fonts/manrope-latin.woff2') }}') format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD}
        @font-face{font-family:'Manrope';font-style:normal;font-weight:600;font-display:swap;src:url('{{ asset('fonts/manrope-latin-ext.woff2') }}') format('woff2');unicode-range:U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF}
        @font-face{font-family:'Manrope';font-style:normal;font-weight:600;font-display:swap;src:url('{{ asset('fonts/manrope-latin.woff2') }}') format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD}
        @font-face{font-family:'Manrope';font-style:normal;font-weight:700;font-display:swap;src:url('{{ asset('fonts/manrope-latin-ext.woff2') }}') format('woff2');unicode-range:U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF}
        @font-face{font-family:'Manrope';font-style:normal;font-weight:700;font-display:swap;src:url('{{ asset('fonts/manrope-latin.woff2') }}') format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD}
        @font-face{font-family:'Manrope';font-style:normal;font-weight:800;font-display:swap;src:url('{{ asset('fonts/manrope-latin-ext.woff2') }}') format('woff2');unicode-range:U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF}
        @font-face{font-family:'Manrope';font-style:normal;font-weight:800;font-display:swap;src:url('{{ asset('fonts/manrope-latin.woff2') }}') format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD}
        :root{font-family:'DM Sans',sans-serif;color:#1f2937;background:#fafaf9;font-synthesis:none;text-rendering:optimizeLegibility;--ink:#1f2937;--forest:#2f5233;--forest-dark:#1e3a22;--moss:#4a6741;--sand:#f5f5f4;--paper:#ffffff;--line:#e7e5e4;--muted:#78716c;--accent:#059669;--accent-hover:#047857;--success:#10b981;--warning:#f59e0b;--error:#dc2626;--bg:#fafaf9;--shadow:0 1px 2px rgba(0,0,0,0.05);--shadow-lg:0 4px 6px -1px rgba(0,0,0,0.1),0 2px 4px -1px rgba(0,0,0,0.06)}*{box-sizing:border-box}body{margin:0;min-width:320px;line-height:1.6;-webkit-font-smoothing:antialiased}a{color:inherit;text-decoration:none;transition:color .2s}button,input,select,textarea{font:inherit}button,a,input,select,textarea{outline-offset:2px}button:focus-visible,a:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible{outline:2px solid var(--accent);outline-offset:2px}button{cursor:pointer}.wrap{width:min(1200px,calc(100% - 48px));margin:auto}.topbar{height:72px;background:rgba(255,255,255,0.95);border-bottom:none;display:flex;align-items:center;position:fixed;top:0;left:0;right:0;z-index:100;transition:background .3s,border .3s,box-shadow .3s}.topbar.scrolled{background:rgba(255,255,255,0.98);border-bottom:1px solid var(--line);box-shadow:0 2px 8px rgba(0,0,0,0.08)}.brand{font-family:Manrope,sans-serif;font-size:22px;font-weight:700;letter-spacing:-0.5px;color:var(--forest-dark)}.brand small{display:block;font:500 9px 'DM Sans',sans-serif;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);margin-top:2px}.navlinks a{color:var(--ink);transition:color .2s}.nav{display:flex;align-items:center;gap:24px;width:min(1200px,calc(100% - 48px));margin:0 auto;height:100%}.brand{font-family:Manrope,sans-serif;font-size:22px;font-weight:700;letter-spacing:-0.5px;color:var(--forest-dark);transition:transform .2s}.brand:hover{transform:scale(1.02)}.brand small{display:block;font:500 9px 'DM Sans',sans-serif;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);margin-top:2px}.navlinks{display:flex;align-items:center;gap:24px;font-size:14px;font-weight:500;flex:1;justify-content:flex-end}.navlinks a:not(.button):not(.nav-account){position:relative;padding:0 0 2px}.navlinks a:not(.button):not(.nav-account)::after{content:'';position:absolute;bottom:0;left:0;width:0;height:2px;background:var(--forest);transition:width .2s}.navlinks a:not(.button):not(.nav-account):hover::after{width:100%}.navlinks .button{padding:8px 16px;background:var(--forest);color:white;border-radius:6px;font-weight:600}.navlinks .button:hover{background:var(--forest-dark)}.nav-form{margin:0}.nav-form button{background:none;border:none;color:var(--ink);padding:8px 12px;font-weight:500;transition:color .2s}.nav-form button:hover{color:var(--forest)}.nav-account{display:flex;align-items:center;gap:10px;padding:6px 12px;border-radius:8px;transition:background .2s}.nav-account:hover{background:var(--sand)}.nav-avatar{width:36px;height:36px;background:var(--forest);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px}.nav-account-copy{display:flex;flex-direction:column;gap:1px}.nav-account-copy strong{font-size:14px;font-weight:600}.nav-account-copy small{font-size:11px;color:var(--muted)}.main{padding-top:72px;min-height:calc(100vh - 72px)}.section-title{font:700 28px Manrope,sans-serif;margin:0;color:var(--ink)}.page-head{margin-bottom:32px}.eyebrow{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin:0 0 8px}.muted{color:var(--muted);font-size:14px}.small-note{font-size:12px;color:var(--muted)}.stack{display:flex;flex-direction:column;gap:16px}.row{display:flex;align-items:center;justify-content:space-between;gap:16px}.panel{background:white;padding:24px;border-radius:10px;box-shadow:var(--shadow)}.panel.empty{text-align:center;color:var(--muted);font-size:14px}.button{display:inline-block;background:var(--forest);color:white;padding:10px 20px;border:none;border-radius:6px;font-weight:600;font-size:14px;text-align:center;transition:background .2s,transform .1s;cursor:pointer}.button:hover{background:var(--forest-dark);transform:translateY(-1px)}.button.secondary{background:var(--sand);color:var(--ink)}.button.secondary:hover{background:var(--line)}.button.small{padding:8px 14px;font-size:13px}.field{display:flex;flex-direction:column;gap:6px}.field label{font-weight:600;font-size:14px;color:var(--ink)}.field input,.field select,.field textarea{padding:10px 12px;border:1px solid var(--line);border-radius:6px;font-size:14px;transition:border-color .2s}.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--forest);outline:none}.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}.status{display:inline-block;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:700;text-transform:uppercase}.status.pending{background:#fef3c7;color:#92400e}.status.approved{background:#d1fae5;color:#065f46}.status.rejected{background:#fee2e2;color:#7f1d1d}.status.cancelled{background:#e0e7ff;color:#3730a3}.footer{background:#142a1b;color:white;padding:48px 0 24px}.footer-content{display:grid;grid-template-columns:repeat(4,1fr);gap:40px;margin-bottom:32px}.footer h4{font:600 14px Manrope,sans-serif;text-transform:uppercase;letter-spacing:1px;margin:0 0 16px}.footer ul{list-style:none;padding:0;margin:0}.footer ul li{margin-bottom:10px}.footer a{color:#d1d5db;transition:color .2s}.footer a:hover{color:white}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse}th,td{padding:12px 16px;text-align:left;border-bottom:1px solid var(--line)}th{font-weight:600;color:var(--forest-dark);background:var(--sand)}tr:hover{background:var(--sand)}
         .nav-account-menu{position:relative}
         .nav-account{border:0;background:none;font:inherit;text-align:left;cursor:pointer}
         .nav-account-dropdown{position:absolute;top:calc(100% + 8px);right:0;z-index:120;display:flex;flex-direction:column;gap:2px;min-width:190px;padding:6px;background:white;border:1px solid var(--line);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.12)}
         .nav-account-dropdown[hidden]{display:none}
         .nav-account-dropdown a,.nav-account-dropdown button{display:block;width:100%;padding:9px 12px;border:0;border-radius:6px;background:none;color:var(--ink);font:500 14px 'DM Sans',sans-serif;text-align:left;cursor:pointer}
         .nav-account-dropdown a:hover,.nav-account-dropdown button:hover{background:var(--sand);color:var(--forest-dark)}
         .nav-account-dropdown form{margin:0}
         @keyframes slideDown{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
         .flash{position:fixed;top:88px;left:50%;transform:translateX(-50%);z-index:200;min-width:400px;max-width:600px;padding:16px 24px;background:#10b981;color:white;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);font-weight:500;animation:slideDown 0.3s ease-out}
         .errors{position:fixed;top:88px;left:50%;transform:translateX(-50%);z-index:200;min-width:400px;max-width:600px;padding:16px 24px;background:#dc2626;color:white;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);animation:slideDown 0.3s ease-out}.errors strong{display:block;margin-bottom:8px;font-weight:600}.errors ul{margin:0;padding-left:20px;line-height:1.6}
         @media(max-width:768px){.wrap{width:calc(100% - 32px)}.topbar{height:auto;padding:12px 0}.nav{flex-direction:column;align-items:flex-start;gap:12px}.navlinks{width:100%;gap:16px;flex-wrap:wrap;font-size:13px}.main{padding:0}.section-title{font-size:24px}.form-grid{grid-template-columns:1fr}.panel{padding:16px}.footer-content{grid-template-columns:1fr;gap:24px}.row.mobile-stack{flex-direction:column;align-items:flex-start}.flash{min-width:auto;max-width:calc(100% - 32px);top:76px}.errors{min-width:auto;max-width:calc(100% - 32px);top:76px}}
    </style>
    @stack('head')
</head>
<body class="@if (request()->routeIs('home')) home-page @endif">
<header class="topbar">
    <nav class="nav" aria-label="Navigasi utama">
        <a class="brand" href="{{ route('home') }}">Rimba Sewa<small>Peralatan perjalanan luar ruang</small></a>
        <div class="navlinks">
            <a href="{{ route('home') }}#alat">Alat sewa</a>
            @if (! auth()->check() || ! in_array(auth()->user()->role, ['admin', 'staff'], true))
                <a href="{{ route('home') }}#cara-pesan">Panduan</a>
            @endif
            @auth
                @if (! in_array(auth()->user()->role, ['admin', 'staff'], true))
                    <a href="{{ route('cart') }}" style="position:relative">
                        Keranjang
                        @if(session('cart') && count(session('cart')) > 0)
                            <span style="position:absolute;top:-8px;right:-12px;background:var(--accent);color:white;font-size:11px;font-weight:700;padding:2px 6px;border-radius:10px;min-width:20px;text-align:center">{{ count(session('cart')) }}</span>
                        @endif
                    </a>
                    <a href="{{ route('bookings.index') }}">Riwayat sewa</a>
                @endif
                @if (in_array(auth()->user()->role, ['staff', 'admin'], true))
                    <a href="{{ route('staff.analytics') }}">Dashboard</a>
                    <a href="{{ route('staff.dashboard') }}">Panel {{ auth()->user()->role === 'admin' ? 'admin' : 'staf' }}</a>
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.assessments') }}">Penilaian staff</a>
                    @endif
                @endif
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a class="button small" href="{{ route('register') }}">Buat akun</a>
            @endauth
        </div>
        @auth
            @php
                $accountRoleLabel = match (auth()->user()->role) {
                    'admin' => 'Admin',
                    'staff' => 'Staff',
                    default => 'Penyewa',
                };
            @endphp
            <div class="nav-account-menu">
                <button class="nav-account" type="button" aria-haspopup="true" aria-expanded="false" onclick="toggleAccountMenu(this)" aria-label="Menu akun {{ auth()->user()->name }}, masuk sebagai {{ $accountRoleLabel }}">
                    <span class="nav-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="nav-account-copy">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>Masuk sebagai {{ $accountRoleLabel }}</small>
                    </span>
                </button>
                <div class="nav-account-dropdown" hidden>
                    <a href="{{ route('profile') }}">Profil</a>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Keluar</button>
                    </form>
                </div>
            </div>
        @endauth
    </nav>
</header>
<main class="main">
    @if (session('success'))<div class="wrap"><div class="flash" role="status">{{ session('success') }}</div></div>@endif
    @if ($errors->any())<div class="wrap"><div class="errors" role="alert"><strong>Periksa kembali isian berikut:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
    @yield('content')
</main>
<footer class="footer"><div class="wrap">
    <div class="footer-content">
        <div>
            <a class="brand" href="{{ route('home') }}" style="display:block;margin-bottom:16px;font-size:24px">Rimba Sewa<small>Peralatan camping terlengkap</small></a>
            <p style="color:#9ca3af;font-size:14px;line-height:1.6;margin:0 0 16px;max-width:300px">Sewa perlengkapan camping berkualitas untuk petualangan outdoor Anda.</p>
            <p style="color:#9ca3af;font-size:13px">Setiap hari, 09:00 - 20:00 WIB</p>
        </div>
        <div>
            <h4>Informasi</h4>
            <ul>
                <li><a href="{{ route('home') }}#alat">Katalog alat</a></li>
                <li><a href="{{ route('home') }}#panduan">Panduan camping</a></li>
                @auth<li><a href="{{ route('cart') }}">Keranjang</a></li>@endauth
            </ul>
        </div>
        <div>
            <h4>Layanan</h4>
            <ul>
                @auth
                    <li><a href="{{ route('profile') }}">Profil & reservasi</a></li>
                @else
                    <li><a href="{{ route('login') }}">Masuk</a></li>
                    <li><a href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
        <div>
            <h4>Kontak</h4>
            <ul>
                <li style="color:#9ca3af">Email: info@rimbasewa.com</li>
                <li style="color:#9ca3af">WhatsApp: 0812-3456-7890</li>
            </ul>
        </div>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,0.1);padding-top:20px;text-align:center">
        <p style="color:#6b7280;font-size:13px;margin:0">© {{ date('Y') }} Rimba Sewa. Sistem demonstrasi penyewaan peralatan camping.</p>
    </div>
</div></footer>
@stack('scripts')
<script>
function toggleItemEdit(elementId) {
    const element = document.getElementById(elementId);
    if (element.style.display === 'none' || element.style.display === '') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

function toggleArticleEdit(elementId) {
    const element = document.getElementById(elementId);
    if (element.style.display === 'none' || element.style.display === '') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded',function(){const topbar=document.querySelector('.topbar');const isHome=document.body.classList.contains('home-page');let scrolledFromTop=false;function updateTopbar(){const shouldBeTransparent=isHome&&scrolledFromTop<600;topbar.classList.toggle('scrolled',scrolledFromTop>=600);topbar.style.background=shouldBeTransparent?'transparent':'rgba(255,255,255,0.95)';if(scrolledFromTop>=600){topbar.style.borderBottom='1px solid var(--line)';topbar.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';topbar.style.color='var(--ink)'}else{topbar.style.borderBottom='none';topbar.style.boxShadow='none'}}window.addEventListener('scroll',function(){scrolledFromTop=window.scrollY;updateTopbar()},{passive:true});updateTopbar();const flash=document.querySelector('.flash');if(flash){setTimeout(()=>{flash.style.transition='opacity 0.3s ease-out';flash.style.opacity='0';setTimeout(()=>flash.remove(),300)},2000)}const errors=document.querySelector('.errors');if(errors){setTimeout(()=>{errors.style.transition='opacity 0.3s ease-out';errors.style.opacity='0';setTimeout(()=>errors.remove(),300)},2000)}});
</script>
<script>
function closeAccountMenu(){document.querySelectorAll('.nav-account-menu').forEach(function(menu){var button=menu.querySelector('.nav-account');var dropdown=menu.querySelector('.nav-account-dropdown');if(button){button.setAttribute('aria-expanded','false')}if(dropdown){dropdown.hidden=true}})}
function toggleAccountMenu(button){var menu=button.closest('.nav-account-menu');var dropdown=menu.querySelector('.nav-account-dropdown');var willOpen=dropdown.hidden;closeAccountMenu();if(willOpen){dropdown.hidden=false;button.setAttribute('aria-expanded','true')}}
document.addEventListener('click',function(event){if(!event.target.closest('.nav-account-menu')){closeAccountMenu()}});
document.addEventListener('keydown',function(event){if(event.key==='Escape'){closeAccountMenu()}});
</script>
</body>
</html>