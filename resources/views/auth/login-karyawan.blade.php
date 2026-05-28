<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('style/admin/login.css') }}">
</head>
<body>

<div class="container">
    <div class="login-box">
        <h1>Login</h1>
        <p>Silakan login untuk mengelola sistem</p>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form action="/login-karyawan" method="POST">
            @csrf

            <div class="input-group">
                <label>Nama</label>
                <input type="text" name="name"
                       placeholder="Masukkan nama"
                       value="{{ old('name') }}" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password"
                       placeholder="Masukkan password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <a href="/login" class="back">← Kembali ke pelanggan</a>
    </div>
</div>

</body>
</html>