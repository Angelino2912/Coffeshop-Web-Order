<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('style/images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('style/images/logo.png') }}">

    <link rel="stylesheet" href="{{ asset('style/admin/login.css') }}">
</head>
<body>

<div class="container">

    <div class="login-box">
        <h1>Login</h1>
        <p>Silakan login untuk mengelola sistem</p>

        <!-- ERROR MESSAGE -->
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- FORM -->
        <form action="/admin/login" method="POST">
            @csrf

            <div class="input-group">
                <label>Email atau Nama</label>
                <input type="text" name="email" placeholder="Masukkan email atau nama" value="{{ old('email') }}" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <a href="/login" class="back">← Kembali ke pelanggan</a>
    </div>

</div>

</body>
</html>
