<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aplikasi Cafe</title>
    <link rel="icon" type="image/png" href="{{ asset('style/images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('style/images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style/admin/sidebar.css') }}">
    <style>
        html, body { margin: 0; padding: 0; height: 100%; }
        .wrapper { display: flex; min-height: 100vh; width: 100%; }
        .content { flex: 1; background-color: #f3dfcf; padding: 40px; box-sizing: border-box; min-width: 0; }
    </style>
    @stack('style')
</head>
<body>
    <div class="wrapper">
        @include('template.sidebar')
        <main class="content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- =====================================================
         AJAX LOADING OVERLAY — dipasang SEBELUM @stack
         ===================================================== --}}
    <div id="ajaxOverlay"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.62);z-index:9999;flex-direction:column;align-items:center;justify-content:center;">

        <svg width="110" height="115" viewBox="0 0 110 115" style="overflow:visible;">
            <g style="animation:ajaxSteam 2.2s ease-in-out infinite;">
                <path d="M30 24 Q25 15 30 6" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-linecap="round"/>
            </g>
            <g style="animation:ajaxSteam 2.2s ease-in-out infinite 0.38s;">
                <path d="M50 20 Q45 11 50 2" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-linecap="round"/>
            </g>
            <g style="animation:ajaxSteam 2.2s ease-in-out infinite 0.76s;">
                <path d="M70 24 Q65 15 70 6" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-linecap="round"/>
            </g>
            <ellipse cx="50" cy="28" rx="36" ry="7" fill="#e8e0d8" stroke="#c8bfb5" stroke-width="1.2"/>
            <path d="M14 28 Q12 55 14 72 Q16 82 22 86 Q34 92 50 92 Q66 92 78 86 Q84 82 86 72 Q88 55 86 28 Z"
                  fill="#ede7e0" stroke="#c8bfb5" stroke-width="1.3"/>
            <path d="M20 32 Q17 55 20 74 Q21 80 24 83"
                  fill="none" stroke="rgba(255,255,255,0.55)" stroke-width="3.5" stroke-linecap="round"/>
            <path d="M84 42 Q100 42 102 55 Q104 68 84 72"
                  fill="none" stroke="#d8d0c8" stroke-width="7" stroke-linecap="round"/>
            <path d="M84 42 Q98 42 100 55 Q102 67 84 72"
                  fill="none" stroke="#ede7e0" stroke-width="4" stroke-linecap="round"/>
            <path d="M84 44 Q99 44 101 55 Q103 66 84 70"
                  fill="none" stroke="#c8bfb5" stroke-width="1" stroke-linecap="round"/>
            <defs>
                <clipPath id="ajaxCupClip">
                    <path d="M14 28 Q12 55 14 72 Q16 82 22 86 Q34 92 50 92 Q66 92 78 86 Q84 82 86 72 Q88 55 86 28 Z"/>
                </clipPath>
            </defs>
            <g clip-path="url(#ajaxCupClip)" style="animation:ajaxLiquid 3s ease-in-out infinite;">
                <rect x="10" y="52" width="80" height="44" fill="#7c4a28"/>
                <ellipse cx="50" cy="52" rx="34" ry="6.5" fill="#9b5c32"/>
                <ellipse cx="44" cy="51" rx="11" ry="2.5" fill="rgba(220,175,110,0.3)"/>
                <ellipse cx="58" cy="53" rx="7" ry="1.8" fill="rgba(220,175,110,0.2)"/>
            </g>
            <ellipse cx="50" cy="28" rx="36" ry="7" fill="none" stroke="#c8bfb5" stroke-width="1.2"/>
            <ellipse cx="50" cy="28" rx="30" ry="5" fill="#5a3018" opacity="0.35"/>
            <ellipse cx="52" cy="108" rx="32" ry="5" fill="rgba(0,0,0,0.28)"/>
        </svg>

        <span id="ajaxOverlayText"
              style="color:#fff;font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;font-weight:600;letter-spacing:0.05em;margin-top:20px;">
            Memproses...
        </span>
        <div style="display:flex;gap:5px;margin-top:8px;">
            <span style="width:5px;height:5px;border-radius:50%;background:#c8a97a;animation:ajaxDot 1.2s ease-in-out infinite;display:block;"></span>
            <span style="width:5px;height:5px;border-radius:50%;background:#c8a97a;animation:ajaxDot 1.2s ease-in-out infinite 0.2s;display:block;"></span>
            <span style="width:5px;height:5px;border-radius:50%;background:#c8a97a;animation:ajaxDot 1.2s ease-in-out infinite 0.4s;display:block;"></span>
        </div>
    </div>

    <style>
        @keyframes ajaxSteam {
            0%   { opacity: 0; transform: translateY(0) scaleX(1); }
            20%  { opacity: 0.7; }
            100% { opacity: 0; transform: translateY(-28px) scaleX(1.5); }
        }
        @keyframes ajaxLiquid {
            0%, 100% { transform: translateX(0); }
            50%       { transform: translateX(1.5px); }
        }
        @keyframes ajaxDot {
            0%, 80%, 100% { opacity: 0.2; transform: scale(0.8); }
            40%            { opacity: 1;   transform: scale(1.1); }
        }
    </style>

    <script>
        function showLoading(text) {
            var el = document.getElementById('ajaxOverlay');
            document.getElementById('ajaxOverlayText').textContent = text || 'Memproses...';
            el.style.display = 'flex';
        }
        function hideLoading() {
            document.getElementById('ajaxOverlay').style.display = 'none';
        }

        (function () {
            var origFetch = window.fetch;
            var skipUrls  = ['/admin/meja/status', '/kasir/meja/status'];

            window.fetch = function (url, opts) {
                var urlStr = typeof url === 'string' ? url : (url?.url ?? '');
                var isSkip = skipUrls.some(function (s) { return urlStr.includes(s); })
                          || (opts && opts._silent === true);

                if (!isSkip) {
                    showLoading(opts && opts._loadingText ? opts._loadingText : 'Memproses...');
                }

                return origFetch.apply(this, arguments).finally(function () {
                    if (!isSkip) hideLoading();
                });
            };
        })();
    </script>

    {{-- custom script halaman dipasang SETELAH interceptor --}}
    @stack('custom_script')

</body>
</html>
