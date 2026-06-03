{{-- resources/views/layouts/landing.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sellvantix - Logiciel de gestion de stock</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* -----------------------------------------------------
           VARIABLES - BLANC, GRIS CLAIR, ORANGE UNIQUEMENT
        ----------------------------------------------------- */
        :root {
            /* Blanc */
            --white: #ffffff;
            
            /* Gris clair */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* Orange */
            --orange-50: #fff7ed;
            --orange-100: #ffedd5;
            --orange-200: #fed7aa;
            --orange-300: #fdba74;
            --orange-400: #fb923c;
            --orange-500: #f97316;
            --orange-600: #ea580c;
            --orange-700: #c2410c;
            
            /* Application */
            --bg-body: var(--gray-50);
            --text-primary: var(--gray-900);
            --text-secondary: var(--gray-600);
            --text-tertiary: var(--gray-400);
            --border: var(--gray-200);
            --border-soft: var(--gray-300);
            --accent: var(--orange-500);
            --accent-light: var(--orange-50);
            --accent-soft: var(--orange-200);
            --accent-gradient: linear-gradient(135deg, var(--orange-500), var(--orange-600));
            
            /* Ombres */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-orange: 0 10px 25px -5px rgba(249, 115, 22, 0.3);
            
            /* Arrondis */
            --radius: 24px;
            --radius-sm: 12px;
            --radius-full: 9999px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* =====================================================
           NAVBAR - BLANC, GRIS CLAIR, ORANGE
        ===================================================== */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            font-size: 28px;
            font-weight: 800;
            color: var(--accent);
            letter-spacing: -0.5px;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand:hover {
            color: var(--orange-600);
        }

        .navbar-brand svg rect {
            transition: fill 0.2s;
        }

        .navbar-brand:hover svg rect {
            fill: var(--orange-600);
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 15px;
            transition: color 0.2s;
            position: relative;
        }

        .nav-link:hover {
            color: var(--accent);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.2s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-login {
            padding: 10px 24px;
            background: transparent;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-full);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-login:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-light);
        }

        .btn-signup {
            padding: 10px 24px;
            background: var(--accent);
            border: none;
            border-radius: var(--radius-full);
            color: var(--white);
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: var(--shadow-orange);
        }

        .btn-signup:hover {
            background: var(--orange-600);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -8px rgba(249, 115, 22, 0.4);
        }

        /* Boutons mobiles — cachés sur desktop */
        .navbar-actions-mobile {
            display: none;
        }

        /* Menu mobile */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 6px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 8px;
        }

        .menu-toggle span {
            width: 24px;
            height: 2px;
            background: var(--text-primary);
            transition: all 0.2s;
        }

        /* =====================================================
           FOOTER - GRIS FONCÉ
        ===================================================== */
        footer {
            background: var(--gray-900);
            color: var(--white);
            padding: 64px 0 32px;
            margin-top: 80px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 48px;
        }

        .footer-brand h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 16px;
        }

        .footer-brand p {
            color: var(--gray-400);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .social-links {
            display: flex;
            gap: 12px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: var(--gray-800);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-400);
            transition: all 0.2s;
        }

        .social-link:hover {
            background: var(--accent);
            color: var(--white);
            transform: translateY(-2px);
        }

        .footer-col h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--white);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: var(--gray-400);
            font-size: 14px;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        .footer-bottom {
            padding-top: 32px;
            border-top: 1px solid var(--gray-800);
            text-align: center;
            color: var(--gray-500);
            font-size: 13px;
        }

        /* =====================================================
           UTILITAIRES
        ===================================================== */
        .text-accent { color: var(--accent); }
        .bg-accent-light { background: var(--accent-light); }
        .border-accent { border-color: var(--accent); }

        /* =====================================================
           RESPONSIVE
        ===================================================== */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .navbar-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--white);
                padding: 24px;
                flex-direction: column;
                gap: 16px;
                border-bottom: 1px solid var(--border);
                box-shadow: var(--shadow-lg);
            }

            .navbar-menu.active {
                display: flex;
            }

            .navbar-actions {
                display: none;
            }

            .navbar-actions-mobile {
                display: flex;
                flex-direction: column;
                gap: 10px;
                width: 100%;
                padding-top: 8px;
                border-top: 1px solid var(--border);
                margin-top: 4px;
            }

            .navbar-actions-mobile .btn-login,
            .navbar-actions-mobile .btn-signup {
                text-align: center;
                display: block;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }
        }
    </style>
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

    {{-- NAVBAR --}}
    <nav class="navbar">
        <div class="container">
            <a href="{{ route('landing') }}" class="navbar-brand" style="display:flex;align-items:center;gap:10px;">
                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect width="36" height="36" rx="10" fill="#f97316"/>
                    <!-- boîte -->
                    <path d="M9 14l9-5 9 5v10l-9 5-9-5V14z" fill="white" fill-opacity="0.15" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                    <!-- arête centrale -->
                    <path d="M18 9v18" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                    <!-- pli gauche -->
                    <path d="M9 14l9 5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                    <!-- pli droit -->
                    <path d="M27 14l-9 5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                    <!-- bande diagonale -->
                    <path d="M13 11.5l10 5.5" stroke="white" stroke-width="1" stroke-linecap="round" opacity="0.6"/>
                </svg>
                <span style="color: var(--gray-900);">Sell<span style="color: var(--accent);">vantix</span></span>
            </a>

            <button class="menu-toggle" onclick="document.querySelector('.navbar-menu').classList.toggle('active')">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="navbar-menu">
                <a href="{{ route('landing') }}" class="nav-link">Accueil</a>
                <a href="{{ route('demo') }}" class="nav-link">Démo</a>
                <a href="{{ route('pricing') }}" class="nav-link">Tarifs</a>
                <a href="{{ route('features') }}" class="nav-link">Fonctionnalités</a>
                <a href="{{ route('guide') }}" class="nav-link">Guide</a>
                <a href="{{ route('faq') }}" class="nav-link">FAQ</a>
                <div class="navbar-actions-mobile">
                    <a href="{{ route('login') }}" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Connexion
                    </a>
                    <a href="{{ route('pricing') }}" class="btn-signup">
                        <i class="bi bi-rocket-takeoff"></i>
                        Essai gratuit
                    </a>
                </div>
            </div>

            <div class="navbar-actions">
                <a href="{{ route('login') }}" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Connexion
                </a>
                <a href="{{ route('pricing') }}" class="btn-signup">
                    <i class="bi bi-rocket-takeoff"></i>
                    Essai gratuit
                </a>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3>Sellvantix</h3>
                    <p>Le logiciel complet pour gérer votre stock et vos ventes. Produits, clients, fournisseurs et rapports en un seul endroit.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-github"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Produit</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('demo') }}">Démo</a></li>
                        <li><a href="{{ route('pricing') }}">Tarifs</a></li>
                        <li><a href="{{ route('features') }}">Fonctionnalités</a></li>
                        <li><a href="{{ route('guide') }}">Guide d'utilisation</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Entreprise</h4>
                    <ul class="footer-links">
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Carrières</a></li>
                        <li><a href="mailto:contact@yyamd.com">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Légal</h4>
                    <ul class="footer-links">
                        <li><a href="#">Conditions d'utilisation</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">Mentions légales</a></li>
                        <li><a href="#">CGV</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Support</h4>
                    <ul class="footer-links">
                        <li><a href="#">Centre d'aide</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="mailto:contact@yyamd.com">Support technique</a></li>
                        <li><a href="#">Status</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Sellvantix. Tous droits réservés. | Design par l'équipe Sellvantix</p>
            </div>
        </div>
    </footer>

    {{-- SCRIPTS --}}
    <script>
        // Fermer le menu mobile quand on clique sur un lien
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                document.querySelector('.navbar-menu')?.classList.remove('active');
            });
        });

        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
    <!-- Bouton WhatsApp flottant -->
    <a href="https://wa.me/22990422588?text=Bonjour%2C%20je%20souhaite%20avoir%20plus%20d%27informations%20sur%20Sellvantix."
       target="_blank" rel="noopener noreferrer" class="whatsapp-float" aria-label="Contacter sur WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="white" width="28" height="28">
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
        </svg>
        <span class="whatsapp-tooltip">Contactez-nous</span>
    </a>

    <style>
        .whatsapp-float {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9999;
            background: #25D366;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.45);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 28px rgba(37, 211, 102, 0.6);
        }
        .whatsapp-tooltip {
            position: absolute;
            right: 68px;
            background: #1a1a2e;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .whatsapp-tooltip::after {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-left-color: #1a1a2e;
        }
        .whatsapp-float:hover .whatsapp-tooltip { opacity: 1; }
    </style>

    <script>
        (function () {
            const loader = document.getElementById('page-loader');
            function hideLoader() { loader.classList.add('hidden'); }
            if (document.readyState === 'complete') {
                hideLoader();
            } else {
                window.addEventListener('load', hideLoader);
            }
            window.addEventListener('beforeunload', function () {
                loader.classList.remove('hidden');
            });
        })();
    </script>
</body>
</html>