<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Cafe</title>
    <link rel="icon" type="image/png" href="{{ asset('style/images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('style/images/logo.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @stack('style')
    <style>
        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            min-width: 0;
        }

        img,
        video {
            max-width: 100%;
            height: auto;
        }

        input,
        select,
        textarea,
        button {
            max-width: 100%;
            font: inherit;
        }

        @media (max-width: 768px) {
            body {
                min-height: 100vh;
                height: auto;
                overflow-x: hidden;
            }

            main {
                width: 100%;
            }

            .login-container {
                width: min(100% - 28px, 440px);
                height: auto;
                min-height: 0;
                flex-direction: column;
                margin: 24px auto;
                border-radius: 18px;
            }

            .login-left,
            .login-right {
                width: 100%;
                padding: 26px 22px;
            }

            .login-left h1 {
                font-size: 28px;
            }

            .dashboard-layout {
                flex-direction: column;
            }

            .dashboard-layout .sidebar {
                width: 100%;
                padding: 18px 14px;
            }

            .dashboard-layout .logo {
                margin-bottom: 18px;
            }

            .dashboard-layout .logo h2 {
                font-size: 30px;
            }

            .dashboard-layout .menu-list {
                flex-direction: row;
                overflow-x: auto;
                gap: 10px;
                padding-bottom: 4px;
            }

            .dashboard-layout .menu-item {
                flex: 0 0 auto;
                padding: 12px 14px;
                font-size: 14px;
                border-radius: 10px;
                white-space: nowrap;
            }

            .main-content {
                padding: 18px 14px;
            }

            .welcome-card,
            .box,
            .checkout-box,
            .summary,
            .review-panel,
            .review-done,
            .order-card,
            .empty {
                border-radius: 16px;
                padding: 20px;
            }

            .welcome-card h1,
            .box h1 {
                font-size: 28px !important;
                line-height: 1.2;
            }

            .welcome-card p,
            .box p,
            .box ol {
                font-size: 15px;
            }

            .box,
            .checkout-container,
            .orders-container {
                width: calc(100% - 24px);
                max-width: none;
                margin: 18px auto;
                padding-left: 0;
                padding-right: 0;
            }

            th,
            td,
            .checkout-table th,
            .checkout-table td {
                padding: 12px 10px;
                font-size: 13px;
            }

            .actions,
            .button,
            .checkout-box button {
                width: 100%;
                justify-content: center;
                text-align: center;
            }

            .top {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 420px) {
            .login-left,
            .login-right {
                padding: 22px 18px;
            }

            .category-filters,
            .filter-btn {
                width: 100%;
            }

            .menu {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="hero-bg"></div>
    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack("custom_script")
</body>
</html>
