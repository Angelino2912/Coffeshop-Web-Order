<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir') — Green Grounds Coffee</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('style/kasir/dashboard.css') }}">

    @stack('style')
</head>
<body>

<div class="kasir-app">

    {{-- ===================================================
         TOPBAR — muncul di semua halaman kasir
         =================================================== --}}
    <header class="kasir-topbar">
        <a href="{{ route('kasir.dashboard') }}" class="topbar-logo">
            <div class="topbar-logo-text">
                CAFE CINTA RASA
            </div>
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
            <button class="topbar-btn" id="notifBtn">
                <i class="bi bi-bell"></i>
            </button>
            @if(($pendingCount ?? 0) > 0)
                <span class="topbar-notif-badge">{{ $pendingCount }}</span>
            @endif
        </div>

        <div class="topbar-user">
        </div>
    </header>

    {{-- ===================================================
         SIDEBAR — muncul di semua halaman kasir
         =================================================== --}}
    <aside class="kasir-sidebar">

        {{-- Logo Cafe --}}
        <div class="sidebar-logo-wrap">
            <img src="{{ asset('style/images/logo.png') }}" alt="Café Cinta Rasa" class="sidebar-logo-img">
        </div>

        <span class="sidebar-label">Menu Utama</span>

        <a href="{{ route('kasir.dashboard') }}"
           class="sidebar-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i>
            Dashboard
        </a>

        <a href="{{ route('kasir.meja') }}"
           class="sidebar-item {{ request()->routeIs('kasir.meja') ? 'active' : '' }}">
            <i class="bi bi-layout-three-columns"></i>
            Manajemen Meja
        </a>

        <a href="{{ route('kasir.orders') }}"
           class="sidebar-item {{ request()->routeIs('kasir.orders') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            Riwayat Order
            @if(($pendingCount ?? 0) > 0)
                <span class="sidebar-badge">{{ $pendingCount }}</span>
            @endif
        </a>

        <form method="POST" action="{{ route('kasir.logout') }}" style="margin-top:auto;">
            @csrf
            <button type="submit" class="sidebar-item"
                    style="width:100%;border:none;background:none;text-align:left;cursor:pointer;">
                <i class="bi bi-box-arrow-left"></i>
                Keluar
            </button>
        </form>

    </aside>

    {{-- ===================================================
         KONTEN HALAMAN
         =================================================== --}}
    @yield('content')

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('custom_script')

</body>
</html>