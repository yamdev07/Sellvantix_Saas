<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sellvantix - Gestion de stock')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Styles -->
    @php
        $manifest = public_path('build/manifest.json');
        $cssFile = null; $jsFile = null;
        if (file_exists($manifest)) {
            $manifestData = json_decode(file_get_contents($manifest), true);
            $cssFile = $manifestData['resources/css/app.css']['file'] ?? null;
            $jsFile  = $manifestData['resources/js/app.js']['file'] ?? null;
        }
    @endphp
    @if($cssFile)
        <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
    @endif
    @if($jsFile)
        <script src="{{ asset('build/' . $jsFile) }}" defer></script>
    @endif

    <!-- Styles personnalisés (toujours chargés) -->
    <style>
        :root {
            --orange: #f97316;
            --orange-dark: #ea580c;
            --orange-pale: #fff7ed;
            --orange-soft: #fed7aa;
            --bg: #f1f5f9;
            --card: #ffffff;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --text: #0f172a;
            --text-2: #475569;
            --text-3: #94a3b8;
            --success: #16a34a;
            --danger: #dc2626;
            --info: #2563eb;
            --purple: #7c3aed;
            --violet: #8b5cf6;
            --shadow-sm: 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
            --shadow-md: 0 4px 16px rgba(15,23,42,.08);
            --shadow-orange: 0 8px 24px rgba(249,115,22,.25);
            --radius: 20px;
            --radius-sm: 12px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Badges de rôle */
        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .role-super_admin_global {
            background-color: #8b5cf6;
            color: white;
            border: 1px solid #a78bfa;
        }

        .role-super_admin {
            background-color: #f5f3ff;
            color: #6b21a8;
            border: 1px solid #c084fc;
        }

        .role-admin {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        .role-manager {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #60a5fa;
        }

        .role-cashier {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
        }

        .role-storekeeper {
            background-color: #fff7ed;
            color: #9a3412;
            border: 1px solid #fdba74;
        }

        /* User info bar */
        .user-info-bar {
            background-color: white;
            border-bottom: 1px solid var(--border);
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .user-info-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .user-info-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-details {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .user-name {
            font-weight: 600;
            color: var(--text);
        }

        .user-company {
            color: var(--text-2);
            font-size: 0.75rem;
        }

        .permission-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.125rem 0.5rem;
            background-color: #f1f5f9;
            border-radius: 9999px;
            font-size: 0.7rem;
            color: var(--text-2);
        }

        .permission-badge i {
            font-size: 0.7rem;
        }

        .global-crown {
            color: #8b5cf6;
            margin-right: 2px;
        }

        /* Hexagone */
        .sf-hex {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-orange);
        }

        .sf-hex svg {
            width: 22px;
            height: 22px;
            stroke: #fff;
            fill: none;
        }

        /* Boutons */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            border: none;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            box-shadow: var(--shadow-orange);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(249,115,22,0.4);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .permission-badge {
                display: none;
            }
            .user-company {
                display: none;
            }
        }
    </style>
    
    @yield('styles')
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
    #page-loader.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    .loader-spinner {
        width: 44px;
        height: 44px;
        border: 3px solid #f1f5f9;
        border-top-color: #f97316;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    .loader-logo {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.5px;
    }
    .loader-logo span { color: #f97316; }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
    <div id="page-loader">
        <div class="loader-spinner"></div>
        <div class="loader-logo">Sell<span>vantix</span></div>
    </div>

    <div class="min-h-screen bg-[#f1f5f9]">
        <!-- Navigation Livewire -->
        <livewire:layout.navigation />

        @auth
            <!-- Barre d'information utilisateur -->
            <div class="user-info-bar">
                <div class="user-info-container">
                    <div class="user-info-left">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="user-details">
                            <span class="user-name">{{ auth()->user()->name }}</span>
                            
                            @if(auth()->user()->isSuperAdminGlobal())
                                <span class="role-badge role-super_admin_global">
                                    <i class="bi bi-crown-fill global-crown"></i> Super Admin Global
                                </span>
                            @else
                                <span class="role-badge role-{{ auth()->user()->role }}">
                                    {{ auth()->user()->role_label }}
                                </span>
                            @endif
                            
                            @if(auth()->user()->canManageUsers())
                                <span class="permission-badge">
                                    <i class="bi bi-people"></i> Gère les utilisateurs
                                </span>
                            @endif
                            
                            @if(auth()->user()->canManageStock())
                                <span class="permission-badge">
                                    <i class="bi bi-box-seam"></i> Gère le stock
                                </span>
                            @endif
                            
                            @if(auth()->user()->canManageSales())
                                <span class="permission-badge">
                                    <i class="bi bi-cart"></i> Gère les ventes
                                </span>
                            @endif
                            
                            @if(auth()->user()->canViewReports())
                                <span class="permission-badge">
                                    <i class="bi bi-graph-up"></i> Rapports
                                </span>
                            @endif

                            @if(auth()->user()->isSuperAdminGlobal())
                                <span class="permission-badge" style="background-color:#8b5cf6; color:white;">
                                    <i class="bi bi-globe2"></i> Vue globale
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    @if(auth()->user()->tenant && !auth()->user()->isSuperAdminGlobal())
                        <div class="user-company">
                            <i class="bi bi-building"></i>
                            {{ auth()->user()->tenant->company_name ?? 'Mon Entreprise' }}
                        </div>
                    @endif

                    @if(auth()->user()->isSuperAdminGlobal())
                        <div class="user-company" style="color:#8b5cf6; font-weight:600;">
                            <i class="bi bi-globe2"></i> Toutes les entreprises
                        </div>
                    @endif
                </div>
            </div>
        @endauth

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-8">
            <!-- Messages flash -->
            @foreach (['success', 'error', 'warning', 'info'] as $msg)
                @if(session($msg))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                        <div class="rounded-lg px-4 py-3 mb-4 flex items-center justify-between
                            @if($msg === 'success') bg-green-50 border border-green-200 text-green-700 @endif
                            @if($msg === 'error') bg-red-50 border border-red-200 text-red-700 @endif
                            @if($msg === 'warning') bg-yellow-50 border border-yellow-200 text-yellow-700 @endif
                            @if($msg === 'info') bg-blue-50 border border-blue-200 text-blue-700 @endif"
                            role="alert">
                            <div class="flex items-center gap-2">
                                <i class="bi
                                    @if($msg === 'success') bi-check-circle-fill @endif
                                    @if($msg === 'error') bi-exclamation-circle-fill @endif
                                    @if($msg === 'warning') bi-exclamation-triangle-fill @endif
                                    @if($msg === 'info') bi-info-circle-fill @endif
                                "></i>
                                <span>{{ session($msg) }}</span>
                            </div>
                            <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Message upgrade plan --}}
            @if(session('upgrade'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div style="background:#f5f3ff;border:1.5px solid #c4b5fd;border-radius:12px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <span style="font-size:20px;">🔒</span>
                            <div>
                                <div style="font-weight:700;color:#6d28d9;font-size:14px;margin-bottom:2px;">Fonctionnalité réservée</div>
                                <div style="color:#7c3aed;font-size:13px;">{{ session('upgrade') }}</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                            <a href="{{ route('pricing') }}" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;font-size:13px;font-weight:700;padding:8px 18px;border-radius:30px;text-decoration:none;">
                                Voir les offres →
                            </a>
                            <button onclick="this.closest('div.max-w-7xl').remove()" style="opacity:.5;background:none;border:none;cursor:pointer;font-size:18px;color:#6d28d9;">✕</button>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @yield('scripts')
<script>
    (function () {
        const loader = document.getElementById('page-loader');

        // Hide loader once everything is painted
        function hideLoader() {
            loader.classList.add('hidden');
        }

        if (document.readyState === 'complete') {
            // Fonts + images already loaded (back/forward cache)
            hideLoader();
        } else {
            window.addEventListener('load', hideLoader);
        }

        // Show loader again when the user navigates away
        window.addEventListener('beforeunload', function () {
            loader.classList.remove('hidden');
        });

        // Livewire: show loader on navigation start, hide on finish
        document.addEventListener('livewire:navigating', function () {
            loader.classList.remove('hidden');
        });
        document.addEventListener('livewire:navigated', function () {
            hideLoader();
        });
    })();
</script>
</body>
</html>