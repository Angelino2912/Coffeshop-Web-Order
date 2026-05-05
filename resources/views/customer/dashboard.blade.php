<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Customer</title>
</head>
<body>
<div class="box">
    <h1>Dashboard Customer</h1>
    <p>Selamat datang, {{ $name }}! Pilih menu terbaik untuk pesanan kamu.</p>

    <div class="actions">
        <a href="/menu">Lihat Menu Makanan & Minuman</a>
        <a href="/cart">Lihat Keranjang</a>
        <a href="/checkout">Konfirmasi Pesanan</a>
    </div>

    <div style="margin-top:24px;">
        <h2>Cara Pesan</h2>
        <ol>
            <li>Pilih menu dari halaman <strong>Menu</strong>.</li>
            <li>Tambahkan item ke keranjang.</li>
            <li>Periksa keranjang dan lanjutkan ke checkout.</li>
            <li>Isi nama, nomor HP, nomor meja, dan catatan.</li>
        </ol>
    </div>
</div>
</body>
</html>
