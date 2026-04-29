<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
</head>
<body>

    <h1>Dashboard Admin</h1>

    @if(session('user'))
        <p>Halo, {{ session('user')->name }}</p>
    @endif

    <form action="/logout" method="GET">
        <button type="submit">Logout</button>
    </form>

</body>
</html>