<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Cafe</title>
    <!-- <link rel="icon" href="public\favicon.ico" type="image/x-icon"> -->
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('style/login.css') }}">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">

    <!-- Kanan (Form Login) -->
    <div class="right">
        <h1>Welcome, Please login to your account!</h1>

        <form action="/login" method="POST">
            @csrf

            <label>Email Address</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <a href="#" class="forgot">Forgot Password?</a>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <p class="or">or</p>

        <button class="google">Sign In with Google</button>

        <p class="signup">
            Don’t have an account? <a href="#">Sign Up</a>
        </p>
    </div>

</div>

</body>
</html>