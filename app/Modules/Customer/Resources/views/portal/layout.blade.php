<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('portal-assets/css/portal.css') }}">
    
    <script type="module" src="{{ asset('portal-assets/js/portal.js') }}"></script>
    @vite([
        'resources/css/portal.css', 
        'resources/js/portal/portal.js'
    ])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} | Hotspot Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .duration-circle { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-900">
    <header class="bg-white shadow-sm p-4 sticky top-0 z-50">
        <div class="flex justify-between items-center max-w-lg mx-auto">
            <h1 class="text-orange-600 font-black text-2xl italic tracking-tighter">AMAZONS<span class="text-gray-400 font-light text-sm not-italic ml-1">HOTSPOT</span></h1>
            <div id="status-indicator" class="text-xs font-medium px-2 py-1 rounded bg-red-100 text-red-600">
                <i class="fas fa-circle-dot animate-pulse mr-1"></i> Not Connected
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto pb-20">
        @yield('content')
    </main>

    <script>
        // Capture MikroTik Params
        const urlParams = new URLSearchParams(window.location.search);
        const hotspotParams = ['mac', 'ip', 'ssid', 'link-login-only', 'link-orig', 'error'];
        
        hotspotParams.forEach(param => {
            if (urlParams.has(param)) {
                localStorage.setItem('hs_' + param, urlParams.get(param));
            }
        });

        const HS_MAC = localStorage.getItem('hs_mac');
        const HS_LOGIN_URL = localStorage.getItem('hs_link-login-only');
    </script>
</body>
</html>