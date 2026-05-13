@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-layout">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="logo">
            <h2>Cafe</h2>
            <p>Cinta Rasa</p>
        </div>

        <div class="menu-list">

            <a href="/menu" class="menu-item">
                Lihat Menu
            </a>

            <a href="/cart" class="menu-item">
                Lihat Keranjang
            </a>

            <a href="/checkout" class="menu-item">
                Konfirmasi Pesanan
            </a>

        </div>

    </div>

    <!-- CONTENT -->
    <div class="main-content">

        <div class="welcome-card">
            <h1>
                Selamat Datang,
                {{ session('customer_name') }}!
            </h1>
            <p>
                Pesanan selanjutnya bisa langsung dilanjutkan
                dari halaman ini.
            </p>
        </div>

        <div class="box">
            <h2>Cara Pesan</h2>

            <ol>
                <li>Pilih menu dari halaman Menu.</li>
                <li>Tambahkan item ke keranjang.</li>
                <li>Periksa keranjang dan checkout.</li>
                <li>Tambahkan catatan lalu konfirmasi.</li>
            </ol>
        </div>

    </div>

</div>
@endsection