<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts: Gestion intelligente Local/Production -->
        @php
            // Détection automatique : production si domaine alwaysdata.net OU APP_ENV=production
            $isProduction = str_contains(url(''), 'alwaysdata.net') || app()->environment('production');
            $manifestPath = public_path('build/manifest.json');
            $useCompiled = $isProduction && file_exists($manifestPath);
        @endphp

        @if($useCompiled)
            {{-- Production : chargement direct des assets compilés via manifest --}}
            @php
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
                $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            @endphp
            @if($cssFile)
                <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
            @endif
            @if($jsFile)
                <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
            @endif
        @else
            {{-- Local : Vite dev server --}}
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            html, body {
                margin: 0;
                padding: 0;
                height: 100%;
                overflow: auto;
            }
            body > div {
                min-height: 100vh;
                width: 100%;
                display: flex;
                flex-direction: column;
            }
        </style>

        @stack('styles')
        <style>
            #page-loader {
                position: fixed;
                inset: 0;
                z-index: 99999;
                background: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                gap: 16px;
                transition: opacity 0.25s ease, visibility 0.25s ease;
            }
            #page-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
            .loader-spinner {
                width: 44px;
                height: 44px;
                border: 3px solid #f1f5f9;
                border-top-color: #f97316;
                border-radius: 50%;
                animation: spin 0.7s linear infinite;
            }
            .loader-logo { font-size: 18px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px; }
            .loader-logo span { color: #f97316; }
            @keyframes spin { to { transform: rotate(360deg); } }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div id="page-loader">
            <div class="loader-spinner"></div>
            <div class="loader-logo">Sell<span>vantix</span></div>
        </div>
        <div>
            {{ $slot }}
        </div>
        <script>
            (function () {
                const loader = document.getElementById('page-loader');
                function hideLoader() { loader.classList.add('hidden'); }
                if (document.readyState === 'complete') { hideLoader(); }
                else { window.addEventListener('load', hideLoader); }
                window.addEventListener('beforeunload', function () { loader.classList.remove('hidden'); });
            })();
        </script>
    </body>
</html>