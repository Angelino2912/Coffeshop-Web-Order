@extends('template.customer')

@section('content')
@push('style')
<link rel="stylesheet" href="{{ asset('style/customers/login.css') }}">
@endpush
<div class="login-container">

    <div class="login-left">
        <h1>Café Cinta Rasa</h1>
        <p>Order makanan langsung dari meja kamu</p>
    </div>

    <div class="login-right">
        <h2>Scan QR Meja</h2>

        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif
        <p class="qr-note">
            Pelanggan hanya bisa masuk melalui QR code yang tersedia di meja.
            Setelah QR dipindai, nomor meja akan terbaca otomatis dan menu langsung terbuka.
        </p>
        <a href="/login-karyawan" class="staff-link">Masuk Admin / Kasir</a>

    </div>

</div>
@endsection
