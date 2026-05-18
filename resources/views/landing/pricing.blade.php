{{-- resources/views/landing/pricing.blade.php --}}
@extends('layouts.landing')

@section('content')
<div class="pricing-page">

    {{-- HEADER SECTION --}}
    <section class="pricing-header">
        <div class="header-bg">
            <div class="header-bg-grid"></div>
            <div class="header-bg-glow"></div>
        </div>
        
        <div class="container">
            <div class="header-content">
                <div class="header-badge">
                    <span class="badge-dot"></span>
                    Tarifs simples & transparents
                </div>
                
                <h1 class="header-title">
                    Choisissez la formule<br>
                    <span class="text-gradient">qui vous correspond</span>
                </h1>
                
                <p class="header-description">
                    14 jours d'essai gratuit · Sans carte bancaire · Sans engagement
                </p>
            </div>
        </div>
    </section>

    {{-- PRICING CARDS --}}
    <section class="pricing-cards">
        <div class="container">
            <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

                {{-- Starter --}}
                <div class="pricing-card">
                    <div class="card-header">
                        <div style="display:inline-block;background:#f1f5f9;color:#64748b;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:12px;letter-spacing:.5px;">PETITES ENTREPRISES</div>
                        <h3 class="card-title">Starter</h3>
                        <div class="card-price">
                            <span class="price-number">10 000</span>
                            <span class="price-period">FCFA/mois</span>
                        </div>
                    </div>
                    <div class="card-features">
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Ventes & stock illimités</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>3 utilisateurs</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Clients & fournisseurs</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Rapports de base</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>200 produits max</span></div>
                        <div class="feature-item" style="opacity:.45;"><span class="feature-icon">✗</span><span>Analyse IA</span></div>
                        <div class="feature-item" style="opacity:.45;"><span class="feature-icon">✗</span><span>Export PDF</span></div>
                        <div class="feature-item" style="opacity:.45;"><span class="feature-icon">✗</span><span>Factures normalisées</span></div>
                    </div>
                    <a href="{{ route('register.form', ['plan' => 'starter']) }}" class="card-button" style="background:#f1f5f9;color:#0f172a;border:1.5px solid #e2e8f0;">
                        <span>Commencer</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>

                {{-- Business Mensuel --}}
                <div class="pricing-card">
                    <div class="card-header">
                        <div style="display:inline-block;background:#fff7ed;color:#ea580c;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:12px;letter-spacing:.5px;">MOYENNES ENTREPRISES</div>
                        <h3 class="card-title">Business</h3>
                        <div class="card-price">
                            <span class="price-number">15 000</span>
                            <span class="price-period">FCFA/mois</span>
                        </div>
                    </div>
                    <div class="card-features">
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Tout le plan Starter</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>10 utilisateurs</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Produits illimités</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Analyse IA</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Export PDF</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Factures normalisées</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Support standard</span></div>
                    </div>
                    <a href="{{ route('register.form', ['plan' => 'monthly']) }}" class="card-button">
                        <span>Commencer</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>

                {{-- Pro Trimestriel --}}
                <div class="pricing-card">
                    <div class="card-header">
                        <h3 class="card-title">Pro Trimestriel</h3>
                        <div class="card-price">
                            <span class="price-number">39 900</span>
                            <span class="price-period">FCFA/3 mois</span>
                        </div>
                        <div class="card-saving">Économisez 5 100 FCFA</div>
                    </div>
                    <div class="card-features">
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Tout le Business</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Utilisateurs illimités</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Analyse IA illimitée</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Support prioritaire</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Formation complète offerte</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Mises à jour incluses</span></div>
                    </div>
                    <a href="{{ route('register.form', ['plan' => 'quarterly']) }}" class="card-button">
                        <span>Commencer</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>

                {{-- Pro Semestriel (Populaire) --}}
                <div class="pricing-card popular">
                    <div class="card-popular"><span class="popular-icon">⭐</span> Le plus choisi</div>
                    <div class="card-header">
                        <h3 class="card-title">Pro Semestriel</h3>
                        <div class="card-price">
                            <span class="price-number">79 900</span>
                            <span class="price-period">FCFA/6 mois</span>
                        </div>
                        <div class="card-saving">Économisez 10 100 FCFA</div>
                    </div>
                    <div class="card-features">
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Tout le Pro Trimestriel</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Audit personnalisé</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Sauvegardes quotidiennes</span></div>
                    </div>
                    <a href="{{ route('register.form', ['plan' => 'semester']) }}" class="card-button">
                        <span>Commencer</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>

                {{-- Annuel --}}
                <div class="pricing-card">
                    <div class="card-header">
                        <h3 class="card-title">Annuel</h3>
                        <div class="card-price">
                            <span class="price-number">105 000</span>
                            <span class="price-period">FCFA/an</span>
                        </div>
                        <div class="card-saving">Économisez 75 000 FCFA</div>
                    </div>
                    <div class="card-features">
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Tout le Pro Semestriel</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Support prioritaire dédié</span></div>
                        <div class="feature-item"><span class="feature-icon">✓</span><span>Formation + audit inclus</span></div>
                    </div>
                    <a href="{{ route('register.form', ['plan' => 'yearly']) }}" class="card-button">
                        <span>Commencer</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>

                {{-- Licence à vie --}}
                <div class="pricing-card lifetime-card">
                    <div class="card-popular" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">
                        <span>♾️</span> Paiement unique
                    </div>
                    <div class="card-header">
                        <div style="display:inline-block;background:#f5f3ff;color:#7c3aed;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:12px;letter-spacing:.5px;">LICENCE À VIE</div>
                        <h3 class="card-title" style="color:#7c3aed;">Lifetime</h3>
                        <div class="card-price">
                            <span class="price-number" style="color:#7c3aed;">300 000</span>
                            <span class="price-period">FCFA <em style="font-style:normal;font-weight:700;color:#7c3aed;">une fois</em></span>
                        </div>
                        <div class="card-saving" style="background:#f5f3ff;color:#7c3aed;">Plus jamais d'abonnement</div>
                    </div>
                    <div class="card-features">
                        <div class="feature-item"><span class="feature-icon" style="color:#7c3aed;">✓</span><span>Tout le plan Annuel</span></div>
                        <div class="feature-item"><span class="feature-icon" style="color:#7c3aed;">✓</span><span>Accès illimité à vie</span></div>
                        <div class="feature-item"><span class="feature-icon" style="color:#7c3aed;">✓</span><span>Toutes les mises à jour</span></div>
                        <div class="feature-item"><span class="feature-icon" style="color:#7c3aed;">✓</span><span>Support à vie inclus</span></div>
                        <div class="feature-item"><span class="feature-icon" style="color:#7c3aed;">✓</span><span>Formation + audit offerts</span></div>
                    </div>
                    <a href="{{ route('register.form', ['plan' => 'lifetime']) }}" class="card-button" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);border:none;color:white;box-shadow:0 10px 25px -5px rgba(124,58,237,0.35);">
                        <span>Acquérir la licence</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- COMPARISON TABLE --}}
    <section class="comparison-section">
        <div class="container">
            <div class="comparison-header">
                <h2 class="comparison-title">Comparez les formules</h2>
                <p class="comparison-subtitle">Tous nos plans incluent l'ensemble des fonctionnalités de base</p>
            </div>
            
            <div class="comparison-table">
                <div class="table-row table-header" style="grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr 1fr;">
                    <div class="table-cell">Fonctionnalité</div>
                    <div class="table-cell">Starter<br><small style="font-weight:400;opacity:.7;">10 000/mois</small></div>
                    <div class="table-cell">Business<br><small style="font-weight:400;opacity:.7;">15 000/mois</small></div>
                    <div class="table-cell">Pro Trim.<br><small style="font-weight:400;opacity:.7;">39 900/3 mois</small></div>
                    <div class="table-cell">Pro Sem.<br><small style="font-weight:400;opacity:.7;">79 900/6 mois</small></div>
                    <div class="table-cell">Annuel<br><small style="font-weight:400;opacity:.7;">105 000/an</small></div>
                    <div class="table-cell" style="color:#7c3aed;font-weight:700;">Lifetime<br><small style="font-weight:400;opacity:.7;">300 000 unique</small></div>
                </div>

                @php $r6 = 'grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr 1fr;'; @endphp

                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Ventes & stock</div>
                    <div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">✓</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Nombre de produits</div>
                    <div class="table-cell">200 max</div><div class="table-cell">Illimité</div><div class="table-cell">Illimité</div><div class="table-cell">Illimité</div><div class="table-cell">Illimité</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">Illimité</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Utilisateurs</div>
                    <div class="table-cell">3</div><div class="table-cell">10</div><div class="table-cell">Illimité</div><div class="table-cell">Illimité</div><div class="table-cell">Illimité</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">Illimité</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Clients & fournisseurs</div>
                    <div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">✓</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Rapports</div>
                    <div class="table-cell">Basiques</div><div class="table-cell">Complets</div><div class="table-cell">Complets</div><div class="table-cell">Complets</div><div class="table-cell">Complets</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">Complets</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Analyse IA</div>
                    <div class="table-cell">-</div><div class="table-cell">✓</div><div class="table-cell">Illimitée</div><div class="table-cell">Illimitée</div><div class="table-cell">Illimitée</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">Illimitée</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Export PDF</div>
                    <div class="table-cell">-</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">✓</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Factures normalisées</div>
                    <div class="table-cell">-</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">✓</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Support prioritaire</div>
                    <div class="table-cell">-</div><div class="table-cell">-</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">À vie</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Formation offerte</div>
                    <div class="table-cell">-</div><div class="table-cell">-</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">✓</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Audit personnalisé</div>
                    <div class="table-cell">-</div><div class="table-cell">-</div><div class="table-cell">-</div><div class="table-cell">✓</div><div class="table-cell">✓</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">✓</div>
                </div>
                <div class="table-row" style="{{ $r6 }}">
                    <div class="table-cell">Accès à vie</div>
                    <div class="table-cell">-</div><div class="table-cell">-</div><div class="table-cell">-</div><div class="table-cell">-</div><div class="table-cell">-</div><div class="table-cell" style="color:#7c3aed;font-weight:700;">♾️</div>
                </div>
            </div>
            
            <div class="comparison-note">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Tous les prix sont en FCFA et incluent la TVA.</span>
            </div>
        </div>
    </section>

    {{-- FAQ SECTION --}}
    <section class="faq-section">
        <div class="container">
            <h2 class="faq-title">Questions fréquentes sur les tarifs</h2>
            
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>Comment fonctionne l'essai gratuit ?</h3>
                    <p>Vous bénéficiez de 14 jours d'essai gratuit avec toutes les fonctionnalités. Aucune carte bancaire n'est demandée.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Puis-je changer de formule en cours de route ?</h3>
                    <p>Oui, vous pouvez passer à une formule supérieure à tout moment. La différence vous sera facturée au prorata.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Y a-t-il des frais cachés ?</h3>
                    <p>Non, tout est inclus dans le prix affiché. Pas de frais d'installation, pas de coûts supplémentaires.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Que se passe-t-il après l'essai ?</h3>
                    <p>Vous pouvez choisir de souscrire à l'une de nos formules. Si vous ne le faites pas, votre accès sera simplement désactivé.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Quels moyens de paiement acceptez-vous ?</h3>
                    <p>Carte bancaire, virement, Wave, Orange Money, Free Money.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Puis-je résilier à tout moment ?</h3>
                    <p>Oui, sans pénalité. Vous conservez vos données pendant 30 jours après résiliation.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <h2 class="cta-title">Prêt à digitaliser votre commerce ?</h2>
                <p class="cta-text">Quincaillerie, librairie, épicerie, boutique… rejoignez plus de 200 commerçants qui nous font confiance</p>
                <div class="cta-buttons">
                    <a href="{{ route('register.form') }}" class="cta-btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 3l14 9-14 9V3z"/>
                        </svg>
                        Essai gratuit 14 jours
                    </a>
                    <a href="{{ route('demo') }}" class="cta-btn-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        Voir la démo
                    </a>
                </div>
                <p class="small-note">Sans carte bancaire • Sans engagement</p>
            </div>
        </div>
    </section>

</div>

<style>
/* ============================================================================
   VARIABLES & BASE
   ============================================================================ */
.pricing-page {
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
    --shadow-xl: 0 20px 25px rgba(0,0,0,0.15);
    --shadow-orange: 0 10px 25px -5px rgba(249,115,22,0.3);
    
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
   HEADER
   ============================================================================ */
.pricing-header {
    position: relative;
    padding: 80px 0 48px;
    text-align: center;
    overflow: hidden;
}

.header-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.header-bg-grid {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(var(--gray-200) 1px, transparent 1px),
        linear-gradient(90deg, var(--gray-200) 1px, transparent 1px);
    background-size: 48px 48px;
    mask-image: radial-gradient(circle at 50% 0%, black, transparent 70%);
    opacity: 0.3;
}

.header-bg-glow {
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 50% 0%, var(--orange-100), transparent 70%);
    opacity: 0.5;
}

.header-content {
    position: relative;
    z-index: 2;
    max-width: 720px;
    margin: 0 auto;
}

.header-badge {
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

.header-title {
    font-size: clamp(36px, 5vw, 48px);
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 16px;
    color: var(--gray-900);
}

.text-gradient {
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.header-description {
    font-size: 18px;
    color: var(--gray-600);
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ============================================================================
   PRICING CARDS
   ============================================================================ */
.pricing-cards {
    padding: 48px 0 64px;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    align-items: start;
}

.pricing-card {
    position: relative;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    padding: 32px 24px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.pricing-card:hover {
    transform: translateY(-4px);
    border-color: var(--orange-200);
    box-shadow: var(--shadow-xl);
}

.pricing-card.popular {
    border: 2px solid var(--orange-500);
    box-shadow: var(--shadow-orange);
    transform: scale(1.02);
    z-index: 2;
}

.card-popular {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 16px;
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    border-radius: 100px;
    color: white;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: var(--shadow-md);
}

.card-header {
    text-align: center;
    margin-bottom: 24px;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 12px;
}

.card-price {
    margin-bottom: 8px;
}

.price-number {
    font-size: 36px;
    font-weight: 800;
    color: var(--gray-900);
    line-height: 1;
}

.price-period {
    font-size: 14px;
    color: var(--gray-400);
    margin-left: 4px;
}

.card-saving {
    display: inline-block;
    padding: 4px 12px;
    background: var(--orange-50);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: var(--orange-600);
}

.card-features {
    flex: 1;
    margin-bottom: 24px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px solid var(--gray-100);
    font-size: 14px;
    color: var(--gray-700);
}

.feature-item:last-child {
    border-bottom: none;
}

.feature-icon {
    color: var(--orange-500);
    font-weight: bold;
}

.card-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    background: transparent;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-lg);
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
    text-decoration: none;
}

.card-button:hover {
    border-color: var(--orange-500);
    color: var(--orange-500);
    background: var(--orange-50);
}

.pricing-card.popular .card-button {
    background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
    border: none;
    color: white;
    box-shadow: var(--shadow-orange);
}

.pricing-card.popular .card-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px -8px rgba(249,115,22,0.4);
}

/* ============================================================================
   COMPARISON TABLE
   ============================================================================ */
.comparison-section {
    padding: 64px 0;
    background: var(--gray-50);
}

.comparison-header {
    text-align: center;
    margin-bottom: 48px;
}

.comparison-title {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 12px;
}

.comparison-subtitle {
    font-size: 16px;
    color: var(--gray-600);
}

.comparison-table {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.table-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr;
    border-bottom: 1px solid var(--gray-200);
}

.lifetime-card {
    border: 2px solid #7c3aed;
    box-shadow: 0 10px 25px -5px rgba(124,58,237,0.2);
}
.lifetime-card:hover {
    border-color: #6d28d9;
    box-shadow: 0 15px 30px -8px rgba(124,58,237,0.3);
}

.table-row:last-child {
    border-bottom: none;
}

.table-header {
    background: var(--gray-50);
    font-weight: 600;
    color: var(--gray-700);
}

.table-cell {
    padding: 16px 20px;
    font-size: 14px;
    color: var(--gray-600);
}

.table-header .table-cell {
    color: var(--gray-700);
    font-weight: 600;
}

.comparison-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
    padding: 16px;
    background: var(--orange-50);
    border-radius: var(--radius-lg);
    color: var(--gray-600);
    font-size: 14px;
}

/* ============================================================================
   FAQ SECTION
   ============================================================================ */
.faq-section {
    padding: 80px 0;
    background: var(--white);
}

.faq-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-900);
    text-align: center;
    margin-bottom: 48px;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    max-width: 900px;
    margin: 0 auto;
}

.faq-item {
    padding: 24px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    transition: all 0.2s;
}

.faq-item:hover {
    border-color: var(--orange-200);
    background: var(--white);
}

.faq-item h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 8px;
}

.faq-item p {
    font-size: 14px;
    color: var(--gray-600);
    line-height: 1.6;
}

/* ============================================================================
   CTA SECTION
   ============================================================================ */
.cta-section {
    padding: 64px 0;
    background: linear-gradient(135deg, var(--gray-900), #000000);
}

.cta-card {
    text-align: center;
    color: white;
}

.cta-title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 12px;
    color: white;
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
    box-shadow: var(--shadow-orange);
}

.cta-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px -8px rgba(249,115,22,0.4);
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

.small-note {
    font-size: 13px;
    color: var(--gray-400);
    margin-top: 16px;
}

/* ============================================================================
   RESPONSIVE
   ============================================================================ */
@media (max-width: 1100px) {
    .cards-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .pricing-card.popular {
        transform: scale(1);
    }
}

@media (max-width: 768px) {
    .container {
        padding: 0 20px;
    }
    
    .cards-grid {
        grid-template-columns: 1fr;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .comparison-table {
        overflow-x: auto;
    }
    
    .table-row {
        min-width: 700px;
    }
    
    .faq-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .header-title {
        font-size: 32px;
    }
    
    .header-description {
        font-size: 16px;
    }
}
</style>
@endsection