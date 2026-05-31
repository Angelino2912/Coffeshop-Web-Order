<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') — Cafe Cinta Rasa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('style/kasir/dashboard.css') }}">

    {{--
        FIX: @stack('style') harus ada di dalam <head> dan TIDAK boleh
        dipanggil sebelum layout di-extend oleh child view.
        Pastikan child view menggunakan @extends() di baris pertama.
    --}}
    @stack('style')
</head>
<body>

<div class="kasir-app">

    {{-- TOPBAR --}}
    <header class="kasir-topbar">
        <a href="{{ route('kasir.dashboard') }}" class="topbar-logo">
            <div class="topbar-logo-text">CAFE CINTA RASA</div>
        </a>
        <span class="topbar-date">
            <i class="bi bi-calendar3"></i>
            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </span>
        <div class="topbar-badge">
            <i class="bi bi-bag-check"></i>
            Total: {{ $totalOrders ?? 0 }} Pesanan
        </div>
        <div class="topbar-notif">
            <button class="topbar-btn" id="notifBtn" type="button">
                <i class="bi bi-bell"></i>
            </button>
            @if(($pendingCount ?? 0) > 0)
                <span class="topbar-notif-badge">{{ $pendingCount }}</span>
            @endif
        </div>
        <div class="topbar-user"></div>
    </header>

    {{-- SIDEBAR --}}
    <aside class="kasir-sidebar">
        <div class="sidebar-logo-wrap">
            <img src="{{ asset('style/images/logo.png') }}" alt="Café Cinta Rasa" class="sidebar-logo-img">
        </div>
        <span class="sidebar-label">Menu Utama</span>
        <a href="{{ route('kasir.dashboard') }}"
           class="sidebar-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="{{ route('kasir.meja') }}"
           class="sidebar-item {{ request()->routeIs('kasir.meja') ? 'active' : '' }}">
            <i class="bi bi-layout-three-columns"></i> Manajemen Meja
        </a>
        <a href="{{ route('kasir.orders') }}"
           class="sidebar-item {{ request()->routeIs('kasir.orders') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Riwayat Order
            @if(($pendingCount ?? 0) > 0)
                <span class="sidebar-badge">{{ $pendingCount }}</span>
            @endif
        </a>

        {{-- FIX: form logout pakai @csrf bukan method="POST" tanpa token --}}
        <form method="POST" action="{{ route('kasir.logout') }}" style="margin-top:auto;">
            @csrf
            <button type="submit" class="sidebar-item sidebar-logout">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </button>
        </form>
    </aside>

    {{-- KONTEN HALAMAN --}}
    @yield('content')

</div>{{-- /.kasir-app --}}

{{-- ============================================================
     AJAX LOADING OVERLAY
     Dipasang SEBELUM Bootstrap JS dan SEBELUM @stack('custom_script')
     agar fetch() interceptor sudah aktif saat script halaman berjalan.
     ============================================================ --}}
<div id="ajaxOverlay">
    <div class="ajax-inner">
        <svg width="110" height="115" viewBox="0 0 110 115" style="overflow:visible;" aria-hidden="true">
            {{-- Uap 1 --}}
            <g class="ajax-steam ajax-steam-1">
                <path d="M30 24 Q25 15 30 6" fill="none" stroke="rgba(255,255,255,0.5)"
                      stroke-width="2" stroke-linecap="round"/>
            </g>
            {{-- Uap 2 --}}
            <g class="ajax-steam ajax-steam-2">
                <path d="M50 20 Q45 11 50 2" fill="none" stroke="rgba(255,255,255,0.5)"
                      stroke-width="2" stroke-linecap="round"/>
            </g>
            {{-- Uap 3 --}}
            <g class="ajax-steam ajax-steam-3">
                <path d="M70 24 Q65 15 70 6" fill="none" stroke="rgba(255,255,255,0.5)"
                      stroke-width="2" stroke-linecap="round"/>
            </g>

            {{-- Bibir cangkir (atas) --}}
            <ellipse cx="50" cy="28" rx="36" ry="7" fill="#e8e0d8" stroke="#c8bfb5" stroke-width="1.2"/>

            {{-- Badan cangkir --}}
            <path d="M14 28 Q12 55 14 72 Q16 82 22 86 Q34 92 50 92 Q66 92 78 86 Q84 82 86 72 Q88 55 86 28 Z"
                  fill="#ede7e0" stroke="#c8bfb5" stroke-width="1.3"/>

            {{-- Highlight sisi kiri --}}
            <path d="M20 32 Q17 55 20 74 Q21 80 24 83"
                  fill="none" stroke="rgba(255,255,255,0.55)" stroke-width="3.5" stroke-linecap="round"/>

            {{-- Gagang cangkir --}}
            <path d="M84 42 Q100 42 102 55 Q104 68 84 72"
                  fill="none" stroke="#d8d0c8" stroke-width="7" stroke-linecap="round"/>
            <path d="M84 42 Q98 42 100 55 Q102 67 84 72"
                  fill="none" stroke="#ede7e0" stroke-width="4" stroke-linecap="round"/>
            <path d="M84 44 Q99 44 101 55 Q103 66 84 70"
                  fill="none" stroke="#c8bfb5" stroke-width="1" stroke-linecap="round"/>

            {{-- Clip path untuk cairan kopi --}}
            <defs>
                <clipPath id="ajaxCupClip">
                    <path d="M14 28 Q12 55 14 72 Q16 82 22 86 Q34 92 50 92 Q66 92 78 86 Q84 82 86 72 Q88 55 86 28 Z"/>
                </clipPath>
            </defs>

            {{-- Cairan kopi --}}
            <g clip-path="url(#ajaxCupClip)" class="ajax-liquid">
                <rect x="10" y="52" width="80" height="44" fill="#7c4a28"/>
                <ellipse cx="50" cy="52" rx="34" ry="6.5" fill="#9b5c32"/>
                <ellipse cx="44" cy="51" rx="11" ry="2.5" fill="rgba(220,175,110,0.3)"/>
                <ellipse cx="58" cy="53" rx="7" ry="1.8" fill="rgba(220,175,110,0.2)"/>
            </g>

            {{-- Rim overlay --}}
            <ellipse cx="50" cy="28" rx="36" ry="7" fill="none" stroke="#c8bfb5" stroke-width="1.2"/>
            <ellipse cx="50" cy="28" rx="30" ry="5" fill="#5a3018" opacity="0.35"/>

            {{-- Bayangan bawah --}}
            <ellipse cx="52" cy="108" rx="32" ry="5" fill="rgba(0,0,0,0.28)"/>
        </svg>

        <span id="ajaxOverlayText">Memproses...</span>

        <div class="ajax-dots">
            <span class="ajax-dot ajax-dot-1"></span>
            <span class="ajax-dot ajax-dot-2"></span>
            <span class="ajax-dot ajax-dot-3"></span>
        </div>
    </div>
</div>

{{-- ============================================================
     STYLES — overlay & animasi (inline agar tidak bergantung pada CSS external)
     ============================================================ --}}
<style>
    /* Overlay */
    #ajaxOverlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.62);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    #ajaxOverlay.is-visible {
        display: flex;
    }
    .ajax-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Teks */
    #ajaxOverlayText {
        color: #fff;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.05em;
        margin-top: 20px;
    }

    /* Dots */
    .ajax-dots {
        display: flex;
        gap: 5px;
        margin-top: 8px;
    }
    .ajax-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #c8a97a;
        display: block;
    }

    /* Sidebar logout button style */
    .sidebar-logout {
        width: 100%;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        color: inherit;
        font: inherit;
        padding: 0;
    }

    /* ── Animasi ── */
    @keyframes ajaxSteam {
        0%   { opacity: 0; transform: translateY(0) scaleX(1); }
        20%  { opacity: 0.7; }
        100% { opacity: 0; transform: translateY(-28px) scaleX(1.5); }
    }
    @keyframes ajaxLiquid {
        0%, 100% { transform: translateX(0); }
        50%      { transform: translateX(1.5px); }
    }
    @keyframes ajaxDot {
        0%, 80%, 100% { opacity: 0.2; transform: scale(0.8); }
        40%            { opacity: 1;   transform: scale(1.1); }
    }

    .ajax-steam   { animation: ajaxSteam  2.2s ease-in-out infinite; }
    .ajax-steam-2 { animation-delay: 0.38s; }
    .ajax-steam-3 { animation-delay: 0.76s; }
    .ajax-liquid  { animation: ajaxLiquid 3s   ease-in-out infinite; }
    .ajax-dot-1   { animation: ajaxDot    1.2s ease-in-out infinite; }
    .ajax-dot-2   { animation: ajaxDot    1.2s ease-in-out infinite 0.2s; }
    .ajax-dot-3   { animation: ajaxDot    1.2s ease-in-out infinite 0.4s; }
</style>

{{-- ============================================================
     Bootstrap JS
     ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
    /* ── Helper: tampil / sembunyikan overlay ── */
    function showLoading(text) {
        var overlay = document.getElementById('ajaxOverlay');
        document.getElementById('ajaxOverlayText').textContent = text || 'Memproses...';
        overlay.classList.add('is-visible');
    }
    function hideLoading() {
        document.getElementById('ajaxOverlay').classList.remove('is-visible');
    }

    /* ── Intercept fetch() ── */
    (function () {
        var origFetch = window.fetch;

        /* URL yang TIDAK perlu menampilkan overlay (polling status meja, dsb.) */
        var skipUrls = ['/admin/meja/status', '/kasir/meja/status'];

        window.fetch = function (input, init) {
            var urlStr = '';
            if (typeof input === 'string') {
                urlStr = input;
            } else if (input instanceof Request) {
                urlStr = input.url;
            }

            var isSilent = (init && init._silent === true);
            var isSkip   = isSilent || skipUrls.some(function (s) {
                return urlStr.indexOf(s) !== -1;
            });

            if (!isSkip) {
                showLoading(init && init._loadingText ? init._loadingText : 'Memproses...');
            }

            return origFetch.apply(this, arguments).finally(function () {
                if (!isSkip) {
                    hideLoading();
                }
            });
        };
    }());
</script>


--}}
@stack('custom_script')

</body>
</html>