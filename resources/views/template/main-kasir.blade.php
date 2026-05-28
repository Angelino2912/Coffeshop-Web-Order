<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Aplikasi Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('style/admin/sidebar.css') }}">
    <style>
        html, body { margin: 0; padding: 0; height: 100%; }
        .wrapper { display: flex; min-height: 100vh; width: 100%; }
        .content {
            flex: 1;
            background-color: #f3dfcf;
            padding: 40px;
            box-sizing: border-box;
            min-width: 0;
        }
    </style>
    @stack('style')
</head>
<body>
    <div class="wrapper">
        @include('template.sidebar-kasir')
        <main class="content">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('custom_script')
</body>
</html>