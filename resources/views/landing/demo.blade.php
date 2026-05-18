{{-- resources/views/landing/demo.blade.php --}}
@extends('layouts.landing')

@section('content')
<div class="demo-page">

    {{-- HERO SECTION --}}
    <section class="demo-hero">
        <div class="hero-bg">
            <div class="hero-bg-grid"></div>
            <div class="hero-bg-glow"></div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="badge-dot"></span>
                    Démo interactive
                </div>
                
                <h1 class="hero-title">
                    Découvrez <span class="text-gradient">Sellvantix</span>
                </h1>
                
                <p class="hero-description">
                    Explorez les fonctionnalités principales de notre logiciel à travers cette démo interactive.
                    Sans inscription, sans engagement.
                </p>
            </div>
        </div>
    </section>

    {{-- TABS --}}
    <section class="demo-tabs">
        <div class="container">
            <div class="tabs-wrapper">
                <button class="tab-btn active" data-tab="stock">
                    <svg class="tab-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                    <span>Stock</span>
                </button>
                
                <button class="tab-btn" data-tab="sales">
                    <svg class="tab-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <span>Ventes</span>
                </button>
                
                <button class="tab-btn" data-tab="clients">
                    <svg class="tab-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Clients</span>
                </button>
                
                <button class="tab-btn" data-tab="reports">
                    <svg class="tab-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    <span>Rapports</span>
                </button>
                
                <div class="tab-slider" id="tabSlider"></div>
            </div>
        </div>
    </section>

    {{-- PANELS --}}
    <section class="demo-panels">
        <div class="container">
            
            {{-- STOCK PANEL --}}
            <div class="demo-panel active" id="stock-panel">
                <div class="panel-grid">
                    <div class="panel-visual">
                        <div class="visual-card">
                            <div class="card-header">
                                <div class="header-dots">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="header-title">Catalogue produits</span>
                            </div>
                            
                            <div class="card-body">
                                <div class="stock-list">
                                    <div class="stock-item">
                                        <div class="item-info">
                                            <span class="item-icon">📗</span>
                                            <div>
                                                <div class="item-name">Cahier grand format</div>
                                                <div class="item-ref">LIB-042</div>
                                            </div>
                                        </div>
                                        <div class="item-stock success">47</div>
                                        <div class="item-price">800 FCFA</div>
                                    </div>

                                    <div class="stock-item">
                                        <div class="item-info">
                                            <span class="item-icon">👕</span>
                                            <div>
                                                <div class="item-name">T-shirt coton — Taille M</div>
                                                <div class="item-ref">VET-117</div>
                                            </div>
                                        </div>
                                        <div class="item-stock warning">6</div>
                                        <div class="item-price">4 500 FCFA</div>
                                    </div>

                                    <div class="stock-item">
                                        <div class="item-info">
                                            <span class="item-icon">🧴</span>
                                            <div>
                                                <div class="item-name">Huile de table 1L</div>
                                                <div class="item-ref">ALI-055</div>
                                            </div>
                                        </div>
                                        <div class="item-stock critical">3</div>
                                        <div class="item-price">1 200 FCFA</div>
                                    </div>

                                    <div class="stock-item">
                                        <div class="item-info">
                                            <span class="item-icon">🔧</span>
                                            <div>
                                                <div class="item-name">Tournevis cruciforme</div>
                                                <div class="item-ref">QUI-023</div>
                                            </div>
                                        </div>
                                        <div class="item-stock success">24</div>
                                        <div class="item-price">1 500 FCFA</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="visual-badge stock-badge-1">
                            <span class="badge-icon">⚡</span>
                            Mise à jour en temps réel
                        </div>
                        
                        <div class="visual-badge stock-badge-2">
                            <span class="badge-dot-pulse"></span>
                            2 alertes stock bas
                        </div>
                    </div>
                    
                    <div class="panel-content">
                        <div class="content-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                            Gestion de stock
                        </div>
                        
                        <h2 class="content-title">
                            Suivez vos produits <br>
                            <span class="title-accent">en temps réel</span>
                        </h2>
                        
                        <p class="content-text">
                            Visualisez instantanément vos niveaux de stock, recevez des alertes automatiques et gardez un historique complet de tous vos mouvements.
                        </p>
                        
                        <ul class="content-features">
                            <li>
                                <span class="feature-check">✓</span>
                                Alertes automatiques
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Historique des mouvements
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Scan code-barres intégré
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Entrées/sorties en 1 clic
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- SALES PANEL --}}
            <div class="demo-panel" id="sales-panel">
                <div class="panel-grid reverse">
                    <div class="panel-visual">
                        <div class="visual-card">
                            <div class="card-header">
                                <div class="header-dots">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="header-title">Nouvelle vente</span>
                            </div>
                            
                            <div class="card-body">
                                <div class="sale-preview">
                                    <div class="sale-client">
                                        <div class="client-avatar">JD</div>
                                        <div class="client-details">
                                            <div class="client-name">Jean Dupont</div>
                                            <div class="client-status">Client régulier</div>
                                        </div>
                                        <span class="client-badge">🎁 Fidèle</span>
                                    </div>
                                    
                                    <div class="sale-items">
                                        <div class="sale-item">
                                            <span>Marteau ×2</span>
                                            <span>9 000 FCFA</span>
                                        </div>
                                        <div class="sale-item">
                                            <span>Tournevis ×3</span>
                                            <span>3 600 FCFA</span>
                                        </div>
                                        <div class="sale-item">
                                            <span>Clous 2kg</span>
                                            <span>3 600 FCFA</span>
                                        </div>
                                    </div>
                                    
                                    <div class="sale-total">
                                        <span>Total</span>
                                        <span class="total-amount">16 200 FCFA</span>
                                    </div>
                                    
                                    <div class="sale-actions">
                                        <button class="btn-secondary" disabled>Imprimer</button>
                                        <button class="btn-primary" disabled>Valider</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="visual-badge sales-badge">
                            <span class="badge-icon">⚡</span>
                            15 secondes pour facturer
                        </div>
                    </div>
                    
                    <div class="panel-content">
                        <div class="content-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="9" cy="21" r="1"/>
                                <circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            Ventes & Facturation
                        </div>
                        
                        <h2 class="content-title">
                            Créez une facture en<br>
                            <span class="title-accent">15 secondes</span>
                        </h2>
                        
                        <p class="content-text">
                            Un processus de vente fluide, du panier à l'impression. Gérez tous les modes de paiement et fidélisez vos clients.
                        </p>
                        
                        <ul class="content-features">
                            <li>
                                <span class="feature-check">✓</span>
                                Devis et factures
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Tous modes de paiement
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Impression tickets
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Rendu monnaie auto
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- CLIENTS PANEL --}}
            <div class="demo-panel" id="clients-panel">
                <div class="panel-grid">
                    <div class="panel-visual">
                        <div class="visual-card">
                            <div class="card-header">
                                <div class="header-dots">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="header-title">Fichier clients</span>
                            </div>
                            
                            <div class="card-body">
                                <div class="clients-list">
                                    <div class="client-row">
                                        <div class="row-avatar" style="background: linear-gradient(135deg, #f97316, #ea580c)">JD</div>
                                        <div class="row-info">
                                            <strong>Jean Dupont</strong>
                                            <span>12 achats · 135 000 FCFA</span>
                                        </div>
                                    </div>
                                    
                                    <div class="client-row">
                                        <div class="row-avatar" style="background: linear-gradient(135deg, #3b82f6, #2563eb)">MM</div>
                                        <div class="row-info">
                                            <strong>Marie Martin</strong>
                                            <span>8 achats · 89 000 FCFA</span>
                                        </div>
                                    </div>
                                    
                                    <div class="client-row">
                                        <div class="row-avatar" style="background: linear-gradient(135deg, #10b981, #059669)">PL</div>
                                        <div class="row-info">
                                            <strong>Pierre Lambert</strong>
                                            <span>15 achats · 234 000 FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel-content">
                        <div class="content-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Gestion clients
                        </div>
                        
                        <h2 class="content-title">
                            Fidélisez votre<br>
                            <span class="title-accent">clientèle</span>
                        </h2>
                        
                        <p class="content-text">
                            Un CRM simple et puissant. Suivez l'historique d'achats, gérez les crédits et envoyez des promotions ciblées.
                        </p>
                        
                        <ul class="content-features">
                            <li>
                                <span class="feature-check">✓</span>
                                Historique d'achats
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Programme de fidélité
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Envoi de promotions
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Gestion des crédits
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- REPORTS PANEL --}}
            <div class="demo-panel" id="reports-panel">
                <div class="panel-grid reverse">
                    <div class="panel-visual">
                        <div class="visual-card">
                            <div class="card-header">
                                <div class="header-dots">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="header-title">Tableau de bord</span>
                            </div>
                            
                            <div class="card-body">
                                <div class="reports-preview">
                                    <div class="stats-grid">
                                        <div class="stat-box">
                                            <span class="stat-label">Aujourd'hui</span>
                                            <span class="stat-value">124 500</span>
                                            <span class="stat-trend up">+12%</span>
                                        </div>
                                        <div class="stat-box">
                                            <span class="stat-label">Ce mois</span>
                                            <span class="stat-value">2,8M</span>
                                            <span class="stat-trend up">+8%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="chart-container">
                                        <div class="chart-bars">
                                            <div class="bar" style="height: 55%"></div>
                                            <div class="bar" style="height: 38%"></div>
                                            <div class="bar" style="height: 72%"></div>
                                            <div class="bar" style="height: 28%"></div>
                                            <div class="bar" style="height: 65%"></div>
                                            <div class="bar" style="height: 88%"></div>
                                            <div class="bar active" style="height: 48%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel-content">
                        <div class="content-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                            Rapports & Analyses
                        </div>
                        
                        <h2 class="content-title">
                            Prenez des décisions<br>
                            <span class="title-accent">éclairées</span>
                        </h2>
                        
                        <p class="content-text">
                            Des tableaux de bord clairs pour analyser vos ventes, marges et performances. Exportez vos données en un clic.
                        </p>
                        
                        <ul class="content-features">
                            <li>
                                <span class="feature-check">✓</span>
                                CA jour/semaine/mois
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Top produits
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Analyse des marges
                            </li>
                            <li>
                                <span class="feature-check">✓</span>
                                Export Excel/PDF
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="demo-cta">
        <div class="cta-background"></div>
        
        <div class="container">
            <div class="cta-content">
                <div class="cta-badge">
                    <span class="badge-dot"></span>
                    Prêt à commencer ?
                </div>
                
                <h2 class="cta-title">
                    Rejoignez plus de <span class="text-gradient">200 commerçants</span>
                </h2>
                
                <p class="cta-text">
                    Essai gratuit 14 jours · Sans carte bancaire · Sans engagement
                </p>
                
                <div class="cta-buttons">
                    <a href="{{ route('pricing') }}" class="cta-btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M5 3l14 9-14 9V3z"/>
                        </svg>
                        Essai gratuit
                    </a>
                    
                    <a href="{{ route('landing') }}" class="cta-btn-secondary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <line x1="19" y1="12" x2="5" y2="12"/>
                            <polyline points="12 19 5 12 12 5"/>
                        </svg>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

<style>
/* ============================================================================
   VARIABLES & BASE
   ============================================================================ */
.demo-page {
    --white: #ffffff;
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
    
    --orange-50: #fff7ed;
    --orange-100: #ffedd5;
    --orange-200: #fed7aa;
    --orange-300: #fdba74;
    --orange-400: #fb923c;
    --orange-500: #f97316;
    --orange-600: #ea580c;
    --orange-700: #c2410c;
    
    --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
    
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background: var(--white);
    color: var(--gray-900);
    line-height: 1.5;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 32px;
}

/* ============================================================================
   HERO SECTION
   ============================================================================ */
.demo-hero {
    position: relative;
    padding: 80px 0 48px;
    text-align: center;
    overflow: hidden;
}

.hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.hero-bg-grid {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(var(--gray-200) 1px, transparent 1px),
        linear-gradient(90deg, var(--gray-200) 1px, transparent 1px);
    background-size: 48px 48px;
    mask-image: radial-gradient(circle at 50% 0%, black, transparent 70%);
    opacity: 0.3;
}

.hero-bg-glow {
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 50% 0%, var(--orange-100), transparent 70%);
    opacity: 0.5;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 720px;
    margin: 0 auto;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: var(--orange-50);
    border: 1px solid var(--orange-200);
    border-radius: 100px;
    font-size: 13px;
    font-weight: 600;
    color: var(--orange-600);
    margin-bottom: 24px;
}

.badge-dot {
    width: 6px;
    height: 6px;
    background: var(--orange-500);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

.hero-title {
    font-size: clamp(40px, 5vw, 56px);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 16px;
    color: var(--gray-900);
}

.text-gradient {
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-description {
    font-size: 18px;
    color: var(--gray-600);
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ============================================================================
   TABS
   ============================================================================ */
.demo-tabs {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--gray-200);
}

.tabs-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    position: relative;
    padding: 16px 0;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: transparent;
    border: none;
    border-radius: var(--radius-lg);
    font-size: 15px;
    font-weight: 500;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    z-index: 2;
}

.tab-btn:hover {
    color: var(--orange-600);
    background: var(--orange-50);
}

.tab-btn.active {
    color: var(--orange-600);
    font-weight: 600;
}

.tab-icon {
    transition: transform 0.2s;
}

.tab-btn:hover .tab-icon {
    transform: translateY(-1px);
}

.tab-slider {
    position: absolute;
    bottom: 16px;
    left: 0;
    height: 32px;
    background: var(--orange-100);
    border-radius: var(--radius-lg);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1;
    opacity: 0;
}

.tab-btn.active ~ .tab-slider {
    opacity: 1;
}

/* ============================================================================
   PANELS
   ============================================================================ */
.demo-panels {
    padding: 64px 0;
}

.demo-panel {
    display: none;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.demo-panel.active {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

.panel-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
}

.panel-grid.reverse {
    direction: rtl;
}

.panel-grid.reverse .panel-content {
    direction: ltr;
}

/* ============================================================================
   VISUAL CARDS
   ============================================================================ */
.panel-visual {
    position: relative;
}

.visual-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-xl);
    transition: transform 0.3s;
}

.visual-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 30px 40px -20px rgba(0,0,0,0.2);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.header-dots {
    display: flex;
    gap: 6px;
}

.header-dots span {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--gray-300);
}

.header-dots span:first-child { background: #ff5f57; }
.header-dots span:nth-child(2) { background: #ffbd2e; }
.header-dots span:nth-child(3) { background: #28c840; }

.header-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    letter-spacing: -0.01em;
}

.card-body {
    padding: 20px;
}

/* Stock Items */
.stock-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.stock-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-100);
    transition: all 0.2s;
}

.stock-item:hover {
    border-color: var(--orange-200);
    background: var(--orange-50);
}

.item-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.item-icon {
    font-size: 18px;
}

.item-name {
    font-weight: 500;
    font-size: 14px;
    color: var(--gray-800);
    margin-bottom: 2px;
}

.item-ref {
    font-size: 11px;
    color: var(--gray-400);
    font-family: monospace;
}

.item-stock {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    min-width: 40px;
    text-align: center;
}

.item-stock.critical { background: #fee2e2; color: #dc2626; }
.item-stock.warning { background: var(--orange-100); color: var(--orange-700); }
.item-stock.success { background: #dcfce7; color: #16a34a; }

.item-price {
    font-weight: 600;
    color: var(--gray-700);
    font-size: 14px;
}

/* Sale Preview */
.sale-preview {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 20px;
}

.sale-client {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--gray-200);
    margin-bottom: 16px;
}

.client-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

.client-details {
    flex: 1;
}

.client-name {
    font-weight: 600;
    font-size: 14px;
    color: var(--gray-800);
    margin-bottom: 2px;
}

.client-status {
    font-size: 11px;
    color: var(--gray-400);
}

.client-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    background: var(--orange-100);
    color: var(--orange-700);
    border-radius: 20px;
}

.sale-items {
    margin-bottom: 16px;
}

.sale-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 13px;
    color: var(--gray-600);
    border-bottom: 1px dashed var(--gray-200);
}

.sale-total {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-top: 2px solid var(--gray-200);
    font-weight: 600;
}

.total-amount {
    font-size: 18px;
    font-weight: 700;
    color: var(--orange-600);
}

.sale-actions {
    display: flex;
    gap: 10px;
    margin-top: 16px;
}

.btn-secondary, .btn-primary {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    opacity: 0.7;
}

.btn-secondary {
    background: white;
    border: 1px solid var(--gray-200);
    color: var(--gray-600);
}

.btn-primary {
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    color: white;
}

/* Clients List */
.clients-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.client-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-100);
}

.row-avatar {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 13px;
}

.row-info {
    flex: 1;
}

.row-info strong {
    display: block;
    font-size: 14px;
    color: var(--gray-800);
    margin-bottom: 2px;
}

.row-info span {
    font-size: 11px;
    color: var(--gray-400);
}

/* Reports Preview */
.reports-preview {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.stat-box {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 12px;
    border: 1px solid var(--gray-100);
}

.stat-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-400);
    margin-bottom: 4px;
}

.stat-value {
    display: block;
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 2px;
}

.stat-trend {
    font-size: 11px;
    font-weight: 600;
}

.stat-trend.up { color: #10b981; }

.chart-container {
    padding: 12px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
}

.chart-bars {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    height: 120px;
}

.bar {
    flex: 1;
    background: var(--orange-200);
    border-radius: 4px 4px 0 0;
    transition: height 0.3s;
}

.bar.active {
    background: linear-gradient(180deg, var(--orange-400), var(--orange-600));
}

/* Visual Badges */
.visual-badge {
    position: absolute;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 100px;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-700);
    box-shadow: var(--shadow-md);
    white-space: nowrap;
    z-index: 10;
}

.stock-badge-1 {
    top: -12px;
    right: -12px;
    color: #16a34a;
    border-color: #bbf7d0;
    background: #f0fdf4;
}

.stock-badge-2 {
    bottom: -12px;
    left: -12px;
    color: var(--orange-700);
    border-color: var(--orange-200);
    background: var(--orange-50);
}

.sales-badge {
    bottom: -12px;
    right: -12px;
}

.badge-dot-pulse {
    width: 6px;
    height: 6px;
    background: var(--orange-500);
    border-radius: 50%;
    animation: pulse 1.5s infinite;
}

/* ============================================================================
   PANEL CONTENT
   ============================================================================ */
.panel-content {
    padding: 20px;
}

.content-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: var(--orange-50);
    border-radius: 100px;
    font-size: 13px;
    font-weight: 600;
    color: var(--orange-600);
    margin-bottom: 20px;
    border: 1px solid var(--orange-200);
}

.content-title {
    font-size: clamp(28px, 3vw, 36px);
    font-weight: 700;
    line-height: 1.2;
    color: var(--gray-900);
    margin-bottom: 16px;
}

.title-accent {
    color: var(--orange-500);
}

.content-text {
    font-size: 16px;
    color: var(--gray-600);
    line-height: 1.7;
    margin-bottom: 28px;
}

.content-features {
    list-style: none;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.content-features li {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-700);
}

.feature-check {
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: bold;
}

/* ============================================================================
   CTA SECTION
   ============================================================================ */
.demo-cta {
    position: relative;
    padding: 100px 0;
    overflow: hidden;
}

.cta-background {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--gray-900), #000000);
    pointer-events: none;
}

.cta-background::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 30% 50%, rgba(249,115,22,0.2), transparent 50%);
}

.cta-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.cta-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: rgba(249,115,22,0.2);
    border: 1px solid rgba(249,115,22,0.3);
    border-radius: 100px;
    font-size: 13px;
    font-weight: 600;
    color: var(--orange-400);
    margin-bottom: 24px;
    backdrop-filter: blur(4px);
}

.cta-title {
    font-size: clamp(32px, 4vw, 42px);
    font-weight: 700;
    color: white;
    margin-bottom: 16px;
    line-height: 1.2;
}

.cta-text {
    font-size: 16px;
    color: var(--gray-400);
    margin-bottom: 32px;
}

.cta-buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
}

.cta-btn-primary, .cta-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    border-radius: var(--radius-lg);
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}

.cta-btn-primary {
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    color: white;
    box-shadow: 0 8px 20px rgba(249,115,22,0.3);
}

.cta-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(249,115,22,0.4);
}

.cta-btn-secondary {
    background: transparent;
    border: 1px solid var(--gray-700);
    color: var(--gray-300);
}

.cta-btn-secondary:hover {
    border-color: var(--gray-500);
    color: white;
}

/* ============================================================================
   RESPONSIVE
   ============================================================================ */
@media (max-width: 1024px) {
    .panel-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .panel-grid.reverse {
        direction: ltr;
    }
    
    .visual-badge {
        display: none;
    }
}

@media (max-width: 768px) {
    .container {
        padding: 0 20px;
    }
    
    .tabs-wrapper {
        overflow-x: auto;
        justify-content: flex-start;
        padding: 12px 0;
    }
    
    .tab-btn {
        padding: 8px 16px;
        white-space: nowrap;
    }
    
    .content-features {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
    }
    
    .cta-btn-primary, .cta-btn-secondary {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 32px;
    }
    
    .hero-description {
        font-size: 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.demo-panel');
    const slider = document.getElementById('tabSlider');
    
    function updateSlider(activeTab) {
        if (!slider || !activeTab) return;
        
        const tabRect = activeTab.getBoundingClientRect();
        const tabsRect = activeTab.parentElement.getBoundingClientRect();
        
        slider.style.width = tabRect.width + 'px';
        slider.style.left = (activeTab.offsetLeft) + 'px';
        slider.style.opacity = '1';
    }
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Update tabs
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            // Update slider
            updateSlider(tab);
            
            // Update panels
            const targetId = tab.dataset.tab + '-panel';
            panels.forEach(panel => {
                panel.classList.remove('active');
                if (panel.id === targetId) {
                    panel.classList.add('active');
                }
            });
        });
    });
    
    // Initialize slider
    const activeTab = document.querySelector('.tab-btn.active');
    if (activeTab) {
        updateSlider(activeTab);
    }
    
    // Handle window resize
    window.addEventListener('resize', () => {
        const activeTab = document.querySelector('.tab-btn.active');
        if (activeTab) {
            updateSlider(activeTab);
        }
    });
});
</script>
@endsection