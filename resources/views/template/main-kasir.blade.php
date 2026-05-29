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

    {{-- Ganti kasir.css dengan nama file CSS kamu yang sebenarnya --}}
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
            <div class="topbar-logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#2D5016" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8h1a4 4 0 010 8h-1"/>
                    <path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/>
                    <line x1="6"  y1="1" x2="6"  y2="4"/>
                    <line x1="10" y1="1" x2="10" y2="4"/>
                    <line x1="14" y1="1" x2="14" y2="4"/>
                </svg>
            </div>
            <div class="topbar-logo-text">
                GREEN GROUNDS
                <span>COFFEE</span>
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
            <div class="topbar-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'KS', 0, 2)) }}
            </div>
            <div class="topbar-user-info">
                {{ auth()->user()->name ?? 'Kasir' }}
                <span>Kasir</span>
            </div>
        </div>
    </header>

    {{-- ===================================================
         SIDEBAR — muncul di semua halaman kasir
         Tambah menu baru di sini, tidak perlu di blade lain
         =================================================== --}}
    <aside class="kasir-sidebar">

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

        {{-- Tambah menu baru di sini kalau perlu --}}
        {{-- Contoh:
        <a href="{{ route('kasir.laporan') }}"
           class="sidebar-item {{ request()->routeIs('kasir.laporan') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i>
            Laporan
        </a>
        --}}

        <span class="sidebar-label">Akun</span>

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
         Setiap blade cukup @extends('template.main-kasir')
         lalu isi @section('content') saja.

         Gunakan wrapper yang sesuai:
           • Halaman POS/kasir utama  → <div class="kasir-main"> + <div class="kasir-receipt">
           • Halaman lain (meja, dll) → <div class="kasir-content">
         =================================================== --}}
    @yield('content')

</div>

{{-- Bootstrap JS tetap ada untuk komponen JS jika dibutuhkan --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('custom_script')

</body>
</html>