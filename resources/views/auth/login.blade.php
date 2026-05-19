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
        <h2>Masuk Pelanggan</h2>

        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="/table/confirm">
            @csrf

            <label>Masukkan nama Anda</label>
            <input type="text" name="customer_name" placeholder="Nama Anda" required>
            @if(isset($meja))
                <p>Anda berada di meja no {{ $meja->no_meja }}</p>
            @endif

            <button type="submit">Mulai Pesan</button>
        </form>
        <!-- <form action="/guest-login" method="POST">
            @csrf

            <input type="text" name="nama" placeholder="Nama Anda" required>
            <input type="number" name="no_meja" placeholder="Nomor Meja" required>

            <button type="submit">Mulai Pesan</button>
        </form> -->

    </div>

</div>
@endsection