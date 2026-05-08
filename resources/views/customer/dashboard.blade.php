@extends('template.main')

@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="welcome-card">
            <h1>Dashboard Customer</h1>
            <p>Selamat datang, {{ $name }}! Pesanan selanjutnya bisa langsung dilanjutkan dari halaman ini.</p>
        </div>
        <div class="info-card">
            <h3>Informasi Login</h3>
            <p>Nama dan nomor meja diambil dari login awal, jadi kamu tidak perlu mengisi ulang lagi saat checkout.</p>
        </div>
    </div>

    <div class="actions">
        <a href="/menu" class="button">Lihat Menu Makanan & Minuman</a>
        <a href="/cart" class="button secondary">Lihat Keranjang</a>
        <a href="/checkout" class="button secondary">Konfirmasi Pesanan</a>
    </div>

    <div class="content">
        <div class="box">
            <h3>Cara Pesan</h3>
            <ol>
                <li>Pilih menu dari halaman <strong>Menu</strong>.</li>
                <li>Tambahkan item ke keranjang.</li>
                <li>Periksa keranjang dan lanjutkan ke checkout.</li>
                <li>Tambahkan catatan jika perlu, lalu konfirmasi.</li>
            </ol>
        </div>
    </div>
</div>
@endsection