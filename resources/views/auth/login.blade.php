<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Customer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('style/customers/login.css') }}">
</head>
<body>

<div class="login-container">

    <!-- LEFT (Branding) -->
    <div class="login-left">
        <h1>Café Cinta Rasa</h1>
        <p>Order makanan langsung dari meja kamu 🍵</p>
    </div>

    <!-- RIGHT (Form) -->
    <div class="login-right">
        <h2>Masuk Pelanggan</h2>

        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/guest-login" method="POST">
            @csrf

            <input type="text" name="nama" placeholder="Nama Anda" required>
            <input type="number" name="no_meja" placeholder="Nomor Meja" required>

            <button type="submit">Mulai Pesan</button>
        </form>

    </div>

</div>

</body>
</html>