{{-- resources/views/landing/index.blade.php --}}
@extends('layouts.landing')

@section('content')
<div class="landing-page">

    {{-- ═══════════════════════════════════════════
         HERO SECTION
    ═══════════════════════════════════════════ --}}
    <section class="hero-section" id="hero">
        <div class="hero-bg">
            <div class="hero-orb orb-1"></div>
            <div class="hero-orb orb-2"></div>
            <div class="hero-orb orb-3"></div>
            <canvas id="particles-canvas"></canvas>
        </div>

        <div class="container hero-grid">
            <div class="hero-content">
                <div class="hero-badge animate-in" data-delay="0">
                    <span class="badge-dot"></span>
                    ✨ Pour quincailleries, librairies, épiceries et bien plus
                </div>

                <h1 class="hero-title animate-in" data-delay="100">
                    Gérez votre
                    <span class="typed-wrapper">
                        <span class="text-gradient" id="typed-word">stock</span>
                        <span class="typed-cursor">|</span>
                    </span><br>
                    quel que soit votre commerce
                </h1>

                <p class="hero-subtitle animate-in" data-delay="200">
                    Sellvantix s'adapte à <strong>tout type de commerce de détail</strong> — stocks, ventes, clients et fournisseurs en un seul endroit.
                    Essayez gratuitement pendant <strong>14 jours</strong>, sans engagement.
                </p>

                <div class="hero-actions animate-in" data-delay="300">
                    <a href="{{ route('demo') }}" class="btn-outline btn-large btn-glow-outline">
                        <i class="bi bi-play-circle-fill"></i>
                        Voir la démo
                    </a>
                    <a href="{{ route('pricing') }}" class="btn-primary btn-large btn-glow">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                        Commencer gratuitement
                        <span class="btn-shine"></span>
                    </a>
                </div>

                <div class="hero-stats animate-in" data-delay="400">
                    <div class="stat-item">
                        <span class="stat-value counter" data-target="30" data-suffix="+">0</span>
                        <span class="stat-label">Entreprises</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-value counter" data-target="98" data-suffix="%">0</span>
                        <span class="stat-label">Satisfaction</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-value">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>
            </div>

            <div class="hero-visual animate-in" data-delay="150">
                <div class="dashboard-mockup">
                    <div class="mockup-header">
                        <div class="mockup-dots">
                            <span class="dot red"></span>
                            <span class="dot yellow"></span>
                            <span class="dot green"></span>
                        </div>
                        <div class="mockup-title">Sellvantix — Tableau de bord</div>
                    </div>
                    <div class="mockup-body">
                        <div class="mockup-cards-row">
                            <div class="mockup-stat-card">
                                <div class="msc-icon orange"><i class="bi bi-cart-check-fill"></i></div>
                                <div class="msc-info">
                                    <span class="msc-label">Ventes aujourd'hui</span>
                                    <span class="msc-value" id="live-sales">24</span>
                                </div>
                                <span class="msc-trend up">+12%</span>
                            </div>
                            <div class="mockup-stat-card">
                                <div class="msc-icon green"><i class="bi bi-graph-up-arrow"></i></div>
                                <div class="msc-info">
                                    <span class="msc-label">Chiffre d'affaires</span>
                                    <span class="msc-value" id="live-revenue">180 500</span>
                                </div>
                                <span class="msc-trend up">+8%</span>
                            </div>
                        </div>
                        <div class="mockup-chart-area">
                            <div class="chart-label">Ventes — 7 derniers jours</div>
                            <div class="chart-bars" id="chart-bars">
                                <div class="chart-col"><div class="chart-bar" style="--h:55%"></div><span>Lun</span></div>
                                <div class="chart-col"><div class="chart-bar" style="--h:70%"></div><span>Mar</span></div>
                                <div class="chart-col"><div class="chart-bar" style="--h:45%"></div><span>Mer</span></div>
                                <div class="chart-col"><div class="chart-bar active" style="--h:90%"></div><span>Jeu</span></div>
                                <div class="chart-col"><div class="chart-bar" style="--h:65%"></div><span>Ven</span></div>
                                <div class="chart-col"><div class="chart-bar" style="--h:80%"></div><span>Sam</span></div>
                                <div class="chart-col"><div class="chart-bar" style="--h:72%"></div><span>Dim</span></div>
                            </div>
                        </div>
                        <div class="mockup-alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            3 produits en stock bas — <strong>Commander maintenant</strong>
                        </div>
                    </div>
                </div>
                <div class="floating-badge fb-1">
                    <i class="bi bi-shield-check-fill"></i> Données sécurisées
                </div>
                <div class="floating-badge fb-2">
                    <i class="bi bi-lightning-charge-fill"></i> Temps réel
                </div>
                <div class="floating-badge fb-3">
                    <i class="bi bi-cloud-check-fill"></i> Cloud
                </div>
            </div>
        </div>

        <div class="hero-scroll-hint">
            <span>Découvrir</span>
            <div class="scroll-arrow"></div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         POUR QUI
    ═══════════════════════════════════════════ --}}
    <section class="for-who-section" id="for-who">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">🏪 Pour qui ?</span>
                <h2 class="section-title">Un logiciel qui s'adapte à <span class="text-gradient">votre activité</span></h2>
                <p class="section-subtitle">Quincaillerie, librairie, épicerie, pharmacie… Sellvantix fonctionne pour <strong>tout commerce de détail</strong>.</p>
            </div>

            <div class="business-types-grid">
                @php
                $businesses = [
                    ['icon'=>'🔧','title'=>'Quincaillerie','desc'=>'Outils, matériaux, fournitures de bricolage','color'=>'orange'],
                    ['icon'=>'📚','title'=>'Librairie / Papeterie','desc'=>'Livres, fournitures scolaires, articles de bureau','color'=>'blue'],
                    ['icon'=>'🛒','title'=>'Épicerie / Alimentation','desc'=>'Produits alimentaires, boissons, produits ménagers','color'=>'green'],
                    ['icon'=>'👗','title'=>'Boutique de vêtements','desc'=>'Prêt-à-porter, chaussures, accessoires de mode','color'=>'purple'],
                    ['icon'=>'💊','title'=>'Pharmacie / Para.','desc'=>'Médicaments, cosmétiques, produits de santé','color'=>'teal'],
                    ['icon'=>'🏪','title'=>'Votre commerce','desc'=>"Sellvantix s'adapte à <em>n'importe quelle</em> activité",'color'=>'any'],
                ];
                @endphp

                @foreach($businesses as $i => $b)
                <div class="business-card reveal-card color-{{ $b['color'] }}" data-delay="{{ $i * 80 }}">
                    <div class="business-icon-wrap">
                        <span class="business-icon">{{ $b['icon'] }}</span>
                    </div>
                    <h3>{{ $b['title'] }}</h3>
                    <p>{!! $b['desc'] !!}</p>
                    <div class="bc-glow"></div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         FEATURES
    ═══════════════════════════════════════════ --}}
    <section class="features-section" id="features">
        <div class="features-bg-dots"></div>
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">Fonctionnalités</span>
                <h2 class="section-title">Tout ce dont vous avez <span class="text-gradient">besoin</span></h2>
                <p class="section-subtitle">Un logiciel complet pour gérer votre stock et votre activité efficacement</p>
            </div>

            <div class="features-bento">
                <div class="bento-card bento-wide reveal-left">
                    <div class="bento-icon"><i class="bi bi-box-seam-fill"></i></div>
                    <h3>Gestion de stock avancée</h3>
                    <p>Suivez vos produits en temps réel avec alertes de stock bas, historique des mouvements et rapports automatiques.</p>
                    <div class="bento-demo">
                        <div class="stock-bar-demo">
                            <div class="sb-item">
                                <span>Vis M5 ×200</span>
                                <div class="sb-track"><div class="sb-fill" style="--w:80%; --c:#10b981"></div></div>
                                <span class="sb-status ok">OK</span>
                            </div>
                            <div class="sb-item">
                                <span>Ciment 50kg ×12</span>
                                <div class="sb-track"><div class="sb-fill" style="--w:24%; --c:#f59e0b"></div></div>
                                <span class="sb-status warn">Bas</span>
                            </div>
                            <div class="sb-item">
                                <span>Câble 2.5mm ×2</span>
                                <div class="sb-track"><div class="sb-fill" style="--w:8%; --c:#ef4444"></div></div>
                                <span class="sb-status danger">Critique</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bento-card reveal-right" data-delay="80">
                    <div class="bento-icon orange"><i class="bi bi-cart-check-fill"></i></div>
                    <h3>Ventes & Facturation</h3>
                    <p>Créez des devis, factures et gérez les paiements en quelques clics.</p>
                </div>

                <div class="bento-card reveal-right" data-delay="160">
                    <div class="bento-icon purple"><i class="bi bi-people-fill"></i></div>
                    <h3>Multi-utilisateurs</h3>
                    <p>Gérez les droits de votre équipe : caissiers, magasiniers, gérants.</p>
                </div>

                <div class="bento-card reveal-left" data-delay="80">
                    <div class="bento-icon green"><i class="bi bi-graph-up-arrow"></i></div>
                    <h3>Rapports détaillés</h3>
                    <p>Analysez vos performances avec des graphiques et statistiques exportables.</p>
                </div>

                <div class="bento-card bento-wide reveal-right" data-delay="0">
                    <div class="bento-icon blue"><i class="bi bi-truck-front-fill"></i></div>
                    <h3>Gestion fournisseurs</h3>
                    <p>Centralisez vos commandes et suivez vos relations avec vos fournisseurs.</p>
                    <div class="bento-demo">
                        <div class="supplier-demo">
                            <div class="sd-row"><span class="sd-name">Distrib. Bois & Co</span><span class="sd-tag active">Actif</span></div>
                            <div class="sd-row"><span class="sd-name">Métal Express SARL</span><span class="sd-tag active">Actif</span></div>
                            <div class="sd-row"><span class="sd-name">Import Ciment Pro</span><span class="sd-tag pending">En attente</span></div>
                        </div>
                    </div>
                </div>

                <div class="bento-card bento-tall reveal-up">
                    <div class="bento-icon teal"><i class="bi bi-shield-fill-check"></i></div>
                    <h3>Sécurisé</h3>
                    <p>Données chiffrées, sauvegardes quotidiennes et hébergement sécurisé.</p>
                    <div class="security-rings">
                        <div class="sr sr-1"></div>
                        <div class="sr sr-2"></div>
                        <div class="sr sr-3"></div>
                        <div class="sr-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         STATS ANIMÉES
    ═══════════════════════════════════════════ --}}
    <section class="stats-section">
        <div class="stats-bg-wave"></div>
        <div class="container">
            <div class="stats-grid reveal">
                <div class="stat-big">
                    <span class="stat-number counter" data-target="200" data-suffix="+">0</span>
                    <span class="stat-desc">Clients actifs</span>
                </div>
                <div class="stat-big">
                    <span class="stat-number counter" data-target="50" data-suffix="k+">0</span>
                    <span class="stat-desc">Produits gérés</span>
                </div>
                <div class="stat-big">
                    <span class="stat-number counter" data-target="98" data-suffix="%">0</span>
                    <span class="stat-desc">Satisfaction client</span>
                </div>
                <div class="stat-big">
                    <span class="stat-number">24/7</span>
                    <span class="stat-desc">Support disponible</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         PRICING TEASER
    ═══════════════════════════════════════════ --}}
    <section class="pricing-teaser">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">Tarifs</span>
                <h2 class="section-title">Des prix adaptés à <span class="text-gradient">toutes les tailles</span></h2>
                <p class="section-subtitle">Commencez avec un essai gratuit de 14 jours, sans carte bancaire</p>
            </div>

            <div class="pills-container">
                @php
                $plans = [
                    ['slug'=>'monthly','name'=>'Mensuel','price'=>'15 000','period'=>'/mois','badge'=>null,'popular'=>false],
                    ['slug'=>'quarterly','name'=>'Trimestriel','price'=>'39 900','period'=>'/3 mois','badge'=>'-11%','popular'=>false],
                    ['slug'=>'semester','name'=>'Semestriel','price'=>'79 900','period'=>'/6 mois','badge'=>'-6%','popular'=>true],
                    ['slug'=>'yearly','name'=>'Annuel','price'=>'105 000','period'=>'/an','badge'=>'-42%','popular'=>false],
                    ['slug'=>'lifetime','name'=>'Licence à vie','price'=>'300 000','period'=>'paiement unique','badge'=>'♾️','popular'=>false,'lifetime'=>true],
                ];
                @endphp

                @foreach($plans as $i => $plan)
                <a href="{{ route('pricing') }}?plan={{ $plan['slug'] }}"
                   class="pill-card reveal-card {{ $plan['popular'] ? 'popular' : '' }} {{ isset($plan['lifetime']) ? 'lifetime' : '' }}"
                   data-delay="{{ $i * 80 }}">
                    @if($plan['popular'])
                        <span class="pill-popular">⭐ Populaire</span>
                    @endif
                    @if(isset($plan['lifetime']))
                        <span class="pill-popular lifetime-badge">♾️ À vie</span>
                    @endif
                    <span class="pill-name">{{ $plan['name'] }}</span>
                    <span class="pill-price">{{ $plan['price'] }}</span>
                    <span class="pill-currency">FCFA</span>
                    <span class="pill-period">{{ $plan['period'] }}</span>
                    @if($plan['badge'] && !isset($plan['lifetime']))
                        <span class="pill-badge">{{ $plan['badge'] }}</span>
                    @endif
                </a>
                @endforeach
            </div>

            <div class="teaser-cta reveal">
                <a href="{{ route('pricing') }}" class="btn-primary btn-large btn-glow">
                    Voir tous les détails
                    <i class="bi bi-arrow-right-circle-fill"></i>
                </a>
                <p class="small-note">Sans carte bancaire • Sans engagement • Résiliation en 1 clic</p>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         TESTIMONIALS CAROUSEL
    ═══════════════════════════════════════════ --}}
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">Témoignages</span>
                <h2 class="section-title">Ils nous font <span class="text-gradient">confiance</span></h2>
            </div>

            <div class="testimonials-carousel reveal">
                <div class="tc-track" id="tc-track">
                    @php
                    $testimonials = [
                        ['stars'=>5,'tag'=>'🔧 Quincaillerie','text'=>'Depuis qu\'on utilise Sellvantix, j\'ai réduit le temps de gestion de stock de 70%. Les alertes de rupture nous ont évité bien des problèmes.','name'=>'Jean Dupont','company'=>'Brico Centre','initials'=>'JD'],
                        ['stars'=>5,'tag'=>'📚 Librairie','text'=>'On gère plus de 3 000 références. Sellvantix nous permet de savoir en temps réel ce qu\'il faut réapprovisionner. Un gain de temps énorme.','name'=>'Awa Fall','company'=>'Librairie Lumière','initials'=>'AF'],
                        ['stars'=>5,'tag'=>'🛒 Épicerie','text'=>'Les rapports de vente m\'ont permis d\'augmenter ma marge de 15% en identifiant les produits les plus rentables. Je recommande !','name'=>'Moussa Koné','company'=>'Épicerie du Marché','initials'=>'MK'],
                        ['stars'=>5,'tag'=>'👗 Boutique','text'=>'L\'interface est super intuitive. Mes vendeurs ont appris à s\'en servir en moins d\'une heure. Le support répond toujours vite.','name'=>'Fatou Diallo','company'=>'Mode & Style','initials'=>'FD'],
                        ['stars'=>5,'tag'=>'💊 Pharmacie','text'=>'La gestion des fournisseurs est excellente. Je suis mes commandes et mes stocks de médicaments sans aucun stress. Parfait.','name'=>'Dr. Kofi Mensah','company'=>'Pharmacie Centrale','initials'=>'KM'],
                    ];
                    @endphp

                    @foreach($testimonials as $t)
                    <div class="tc-item">
                        <div class="testimonial-card">
                            <div class="tc-top">
                                <div class="tc-stars">
                                    @for($s=0;$s<$t['stars'];$s++) ★ @endfor
                                </div>
                                <span class="tc-tag">{{ $t['tag'] }}</span>
                            </div>
                            <p class="tc-text">"{{ $t['text'] }}"</p>
                            <div class="tc-author">
                                <div class="tc-avatar">{{ $t['initials'] }}</div>
                                <div>
                                    <strong>{{ $t['name'] }}</strong>
                                    <span>{{ $t['company'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="tc-controls">
                    <button class="tc-btn" id="tc-prev"><i class="bi bi-chevron-left"></i></button>
                    <div class="tc-dots" id="tc-dots"></div>
                    <button class="tc-btn" id="tc-next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         FAQ ACCORDÉON
    ═══════════════════════════════════════════ --}}
    <section class="faq-section">
        <div class="container">
            <div class="section-header reveal">
                <span class="section-badge">FAQ</span>
                <h2 class="section-title">Questions <span class="text-gradient">fréquentes</span></h2>
                <p class="section-subtitle">Tout ce que vous devez savoir sur Sellvantix</p>
            </div>

            <div class="faq-accordion reveal">
                @php
                $faqs = [
                    ['q'=>'Comment fonctionne l\'essai gratuit ?','a'=>'Vous bénéficiez de <strong>14 jours d\'essai gratuit</strong> avec toutes les fonctionnalités. Aucune carte bancaire n\'est demandée. À la fin de l\'essai, choisissez simplement la formule qui vous convient.'],
                    ['q'=>'Puis-je changer de formule à tout moment ?','a'=>'Oui, vous pouvez upgrader ou downgrader à tout moment depuis votre espace client. La différence de prix est calculée au prorata automatiquement.'],
                    ['q'=>'Les données sont-elles sauvegardées ?','a'=>'Oui, sauvegarde automatique <strong>quotidienne</strong> avec chiffrement de bout en bout. Vos données sont hébergées sur des serveurs sécurisés avec redondance.'],
                    ['q'=>'Quels moyens de paiement acceptez-vous ?','a'=>'Carte bancaire (Visa/Mastercard), virement bancaire, <strong>Wave</strong>, <strong>Orange Money</strong>, <strong>MTN Mobile Money</strong> — adaptés au marché ouest-africain.'],
                    ['q'=>'Y a-t-il un engagement contractuel ?','a'=>'Non, aucun engagement. Vous pouvez résilier à tout moment directement depuis votre tableau de bord, en un seul clic.'],
                    ['q'=>'Combien d\'utilisateurs puis-je ajouter ?','a'=>'Vous pouvez ajouter autant d\'utilisateurs que vous souhaitez (caissiers, magasiniers, gérants) et définir leurs droits individuellement.'],
                ];
                @endphp

                @foreach($faqs as $i => $faq)
                <div class="faq-item" data-delay="{{ $i * 60 }}">
                    <button class="faq-question" aria-expanded="false">
                        <span>{{ $faq['q'] }}</span>
                        <div class="faq-icon">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                    </button>
                    <div class="faq-answer">
                        <p>{!! $faq['a'] !!}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         FINAL CTA
    ═══════════════════════════════════════════ --}}
    <section class="final-cta">
        <div class="cta-bg">
            <div class="cta-orb cta-orb-1"></div>
            <div class="cta-orb cta-orb-2"></div>
        </div>
        <div class="container">
            <div class="cta-inner reveal">
                <div class="cta-badge">🚀 Rejoignez 200+ commerçants</div>
                <h2>Prêt à digitaliser<br>votre commerce ?</h2>
                <p>Quincaillerie, librairie, épicerie, boutique… commencez dès aujourd'hui</p>
                <div class="cta-actions">
                    <a href="{{ route('demo') }}" class="btn-outline btn-large btn-glow-outline btn-white-outline">
                        <i class="bi bi-play-circle-fill"></i>
                        Voir la démo
                    </a>
                    <a href="{{ route('pricing') }}" class="btn-primary btn-large btn-glow btn-white-bg">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                        Essai gratuit 14 jours
                        <span class="btn-shine"></span>
                    </a>
                </div>
                <p class="cta-note">Sans carte bancaire • Sans engagement • Résiliation en 1 clic</p>
            </div>
        </div>
    </section>
</div>

<style>
/* ══════════════════════════════════════════════════════════
   VARIABLES & BASE
══════════════════════════════════════════════════════════ */
:root {
    --hero-h: min(100vh, 900px);
}

/* ══════════════════════════════════════════════════════════
   ANIMATIONS
══════════════════════════════════════════════════════════ */
@keyframes fadeInUp    { from { opacity:0; transform:translateY(40px) } to { opacity:1; transform:translateY(0) } }
@keyframes fadeInLeft  { from { opacity:0; transform:translateX(-50px) } to { opacity:1; transform:translateX(0) } }
@keyframes fadeInRight { from { opacity:0; transform:translateX(50px) } to { opacity:1; transform:translateX(0) } }
@keyframes fadeIn      { from { opacity:0 } to { opacity:1 } }
@keyframes float       { 0%,100% { transform:translateY(0) } 50% { transform:translateY(-12px) } }
@keyframes float2      { 0%,100% { transform:translateY(0) } 50% { transform:translateY(-8px) } }
@keyframes spin        { to { transform:rotate(360deg) } }
@keyframes pulse-ring  { 0% { transform:scale(1); opacity:.4 } 100% { transform:scale(1.8); opacity:0 } }
@keyframes blink       { 0%,100% { opacity:1 } 50% { opacity:0 } }
@keyframes shimmer     { 0% { transform:translateX(-100%) } 100% { transform:translateX(200%) } }
@keyframes orb-move    { 0%,100% { transform:translate(0,0) scale(1) } 33% { transform:translate(30px,-20px) scale(1.05) } 66% { transform:translate(-20px,30px) scale(.95) } }
@keyframes bar-grow    { from { height:0 } to { height:var(--h) } }
@keyframes sb-fill     { from { width:0 } to { width:var(--w) } }
@keyframes ring-pulse  { 0%,100% { transform:scale(1); opacity:.3 } 50% { transform:scale(1.1); opacity:.6 } }
@keyframes scroll-bounce { 0%,100% { transform:translateX(-50%) translateY(0) } 50% { transform:translateX(-50%) translateY(6px) } }

/* ══════════════════════════════════════════════════════════
   HERO
══════════════════════════════════════════════════════════ */
.hero-section {
    position: relative;
    min-height: var(--hero-h);
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
    background: #0f0f1a;
    padding: 100px 0 60px;
}

.hero-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
}

#particles-canvas {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: .4;
}

.hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    animation: orb-move 8s ease-in-out infinite;
}
.orb-1 { width:500px; height:500px; background:rgba(249,115,22,.15); top:-100px; right:-100px; animation-delay:0s; }
.orb-2 { width:350px; height:350px; background:rgba(139,92,246,.1); bottom:-50px; left:-50px; animation-delay:3s; }
.orb-3 { width:250px; height:250px; background:rgba(16,185,129,.08); top:50%; left:40%; animation-delay:5s; }

.hero-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    position: relative;
    z-index: 2;
}

/* Hero content */
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(249,115,22,.3);
    border-radius: 40px;
    font-size: 13px;
    font-weight: 600;
    color: rgba(255,255,255,.85);
    margin-bottom: 24px;
    backdrop-filter: blur(8px);
}
.badge-dot {
    width: 8px; height: 8px;
    background: #f97316;
    border-radius: 50%;
    box-shadow: 0 0 8px #f97316;
    animation: blink 2s ease-in-out infinite;
}

.hero-title {
    font-size: clamp(36px, 5vw, 58px);
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 24px;
    color: #fff;
}
.typed-wrapper { position: relative; display: inline-block; }
.typed-cursor {
    display: inline-block;
    color: var(--orange-500);
    animation: blink 1s step-end infinite;
    font-weight: 300;
    margin-left: 2px;
}

.hero-subtitle {
    font-size: 17px;
    color: rgba(255,255,255,.7);
    margin-bottom: 36px;
    line-height: 1.7;
}
.hero-subtitle strong { color: rgba(255,255,255,.95); }

.hero-actions { display: flex; gap: 14px; margin-bottom: 48px; flex-wrap: wrap; }

.btn-glow { position: relative; overflow: hidden; }
.btn-glow::after { content:''; position:absolute; inset:0; border-radius:inherit; box-shadow:0 0 20px rgba(249,115,22,.4); opacity:0; transition:.3s; }
.btn-glow:hover::after { opacity:1; }
.btn-shine {
    position: absolute;
    top: 0; left: 0;
    width: 40%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.2), transparent);
    animation: shimmer 2.5s ease-in-out infinite;
}

.btn-glow-outline { border-color: rgba(255,255,255,.3) !important; color: rgba(255,255,255,.9) !important; background: rgba(255,255,255,.05) !important; }
.btn-glow-outline:hover { border-color: rgba(255,255,255,.7) !important; background: rgba(255,255,255,.1) !important; }

.hero-stats { display: flex; align-items: center; gap: 32px; }
.stat-divider { width:1px; height:40px; background: rgba(255,255,255,.15); }
.stat-item { text-align: center; }
.stat-value { display: block; font-size: 28px; font-weight: 800; color: #fff; }
.hero-stats .stat-label { font-size: 13px; color: rgba(255,255,255,.5); }

/* Dashboard Mockup */
.hero-visual {
    position: relative;
    animation: float 6s ease-in-out infinite;
}

.dashboard-mockup {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    overflow: hidden;
    box-shadow: 0 40px 80px rgba(0,0,0,.4), inset 0 1px 0 rgba(255,255,255,.1);
}

.mockup-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    background: rgba(255,255,255,.04);
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.mockup-dots { display:flex; gap:6px; }
.mockup-dots .dot { width:10px; height:10px; border-radius:50%; }
.mockup-dots .dot.red { background:#ef4444; }
.mockup-dots .dot.yellow { background:#f59e0b; }
.mockup-dots .dot.green { background:#10b981; }
.mockup-title { font-size:12px; color:rgba(255,255,255,.4); font-weight:500; }

.mockup-body { padding: 20px; }

.mockup-cards-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
.mockup-stat-card {
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 12px;
    padding: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.msc-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.msc-icon.orange { background:rgba(249,115,22,.2); color:#f97316; }
.msc-icon.green  { background:rgba(16,185,129,.2); color:#10b981; }
.msc-info { flex: 1; min-width: 0; }
.msc-label { display:block; font-size:10px; color:rgba(255,255,255,.4); margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.msc-value { display:block; font-size:16px; font-weight:700; color:#fff; }
.msc-trend { font-size:11px; font-weight:600; padding:2px 6px; border-radius:6px; white-space:nowrap; }
.msc-trend.up { background:rgba(16,185,129,.15); color:#10b981; }

.mockup-chart-area { background:rgba(255,255,255,.03); border-radius:12px; padding:14px; margin-bottom:12px; }
.chart-label { font-size:10px; color:rgba(255,255,255,.35); margin-bottom:12px; font-weight:500; }
.chart-bars { display: flex; align-items: flex-end; gap: 6px; height: 70px; }
.chart-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; }
.chart-col span { font-size:8px; color:rgba(255,255,255,.3); }
.chart-bar {
    width: 100%;
    height: var(--h);
    background: rgba(249,115,22,.25);
    border-radius: 4px 4px 0 0;
    transition: height .5s ease, background .3s;
}
.chart-bar.active { background: #f97316; box-shadow: 0 0 12px rgba(249,115,22,.5); }

.mockup-alert {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: rgba(239,68,68,.1);
    border: 1px solid rgba(239,68,68,.2);
    border-radius: 10px;
    font-size: 11px;
    color: #fca5a5;
}
.mockup-alert i { color: #ef4444; font-size: 13px; }
.mockup-alert strong { color: #ef4444; cursor:pointer; }

/* Floating badges */
.floating-badge {
    position: absolute;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: rgba(255,255,255,.9);
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-800);
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
    white-space: nowrap;
}
.floating-badge i { color: var(--orange-500); }
.fb-1 { bottom: -20px; left: -30px; animation: float2 4s ease-in-out infinite; animation-delay:.5s; }
.fb-2 { top: -15px; left: 50%; transform:translateX(-50%); animation: float2 4s ease-in-out infinite; animation-delay:1s; }
.fb-3 { bottom: 20px; right: -25px; animation: float2 5s ease-in-out infinite; animation-delay:2s; }

/* Scroll hint */
.hero-scroll-hint {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    z-index: 2;
    color: rgba(255,255,255,.3);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.scroll-arrow {
    width: 20px; height: 20px;
    border-right: 2px solid rgba(255,255,255,.3);
    border-bottom: 2px solid rgba(255,255,255,.3);
    transform: rotate(45deg);
    animation: scroll-bounce 1.5s ease-in-out infinite;
}

/* animate-in (hero specific) */
.animate-in { opacity: 0; animation: fadeInUp .7s ease forwards; }
.animate-in[data-delay="0"]   { animation-delay: .1s; }
.animate-in[data-delay="100"] { animation-delay: .25s; }
.animate-in[data-delay="150"] { animation-delay: .3s; }
.animate-in[data-delay="200"] { animation-delay: .4s; }
.animate-in[data-delay="300"] { animation-delay: .55s; }
.animate-in[data-delay="400"] { animation-delay: .7s; }

/* ══════════════════════════════════════════════════════════
   REVEAL (scroll animations)
══════════════════════════════════════════════════════════ */
.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-card {
    opacity: 0;
    transition: opacity .7s ease, transform .7s ease;
}
.reveal       { transform: translateY(30px); }
.reveal-left  { transform: translateX(-50px); }
.reveal-right { transform: translateX(50px); }
.reveal-up    { transform: translateY(50px); }
.reveal-card  { transform: translateY(30px) scale(.97); }

.revealed {
    opacity: 1 !important;
    transform: none !important;
}

/* ══════════════════════════════════════════════════════════
   SECTION COMMONS
══════════════════════════════════════════════════════════ */
.section-header { text-align:center; max-width:640px; margin:0 auto 64px; }
.section-badge  {
    display:inline-block; padding:6px 16px;
    background:var(--orange-50); border-radius:30px;
    font-size:13px; font-weight:600; color:var(--orange-600);
    margin-bottom:16px; border:1px solid var(--orange-100);
}
.section-title  { font-size:clamp(28px,4vw,40px); font-weight:700; color:var(--gray-900); margin-bottom:14px; }
.section-subtitle { font-size:16px; color:var(--gray-500); line-height:1.7; }
.text-gradient  { background:linear-gradient(135deg, var(--orange-500), var(--orange-600)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }

/* ══════════════════════════════════════════════════════════
   FOR WHO
══════════════════════════════════════════════════════════ */
.for-who-section { padding:100px 0; background:#fff; }

.business-types-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.business-card {
    background: #fff;
    border: 1.5px solid var(--gray-100);
    border-radius: 20px;
    padding: 32px 24px;
    text-align: center;
    cursor: default;
    position: relative;
    overflow: hidden;
    transition: all .4s cubic-bezier(.175,.885,.32,1.275);
}
.business-card:hover { transform: translateY(-8px) scale(1.02); border-color: var(--orange-300); box-shadow: 0 20px 50px rgba(249,115,22,.12); }

.bc-glow {
    position: absolute;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(249,115,22,.15), transparent);
    top: -40px; left: 50%; transform: translateX(-50%);
    opacity: 0;
    transition: opacity .4s;
}
.business-card:hover .bc-glow { opacity: 1; }

.business-icon-wrap {
    width: 72px; height: 72px;
    background: var(--gray-50);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px;
    transition: all .4s;
}
.business-card:hover .business-icon-wrap { background: var(--orange-50); transform: rotate(-5deg) scale(1.1); }
.business-icon { font-size: 36px; line-height: 1; display: block; }

.business-card h3 { font-size: 16px; font-weight: 700; color: var(--gray-900); margin-bottom: 8px; }
.business-card p  { font-size: 13px; color: var(--gray-500); line-height: 1.5; }
.business-card p em { font-style:normal; color:var(--orange-600); font-weight:600; }

.color-any { border: 2px dashed var(--orange-200); background: var(--orange-50); }
.color-any:hover { border-style: solid; }

/* ══════════════════════════════════════════════════════════
   FEATURES BENTO
══════════════════════════════════════════════════════════ */
.features-section {
    padding: 100px 0;
    background: var(--gray-50);
    position: relative;
}

.features-bg-dots {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(var(--gray-200) 1px, transparent 1px);
    background-size: 30px 30px;
    opacity: .5;
}

.features-bento {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: auto auto;
    gap: 20px;
    position: relative;
    z-index: 1;
}

.bento-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: 24px;
    padding: 32px;
    transition: all .4s ease;
    position: relative;
    overflow: hidden;
}
.bento-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(249,115,22,.03), transparent);
    opacity: 0;
    transition: .4s;
}
.bento-card:hover::before { opacity: 1; }
.bento-card:hover { border-color: var(--orange-200); box-shadow: 0 20px 60px rgba(249,115,22,.08); transform: translateY(-4px); }
.bento-wide { grid-column: span 2; }
.bento-tall { grid-row: span 2; }

.bento-icon {
    width: 52px; height: 52px;
    background: var(--orange-50);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
    color: var(--orange-500);
    margin-bottom: 20px;
    transition: all .3s;
}
.bento-icon.orange { background:rgba(249,115,22,.1); color:#f97316; }
.bento-icon.green  { background:rgba(16,185,129,.1); color:#10b981; }
.bento-icon.blue   { background:rgba(59,130,246,.1); color:#3b82f6; }
.bento-icon.purple { background:rgba(139,92,246,.1); color:#8b5cf6; }
.bento-icon.teal   { background:rgba(20,184,166,.1); color:#14b8a6; }
.bento-card:hover .bento-icon { transform: rotate(-8deg) scale(1.1); }

.bento-card h3 { font-size: 18px; font-weight: 700; color: var(--gray-900); margin-bottom: 10px; }
.bento-card p  { font-size: 14px; color: var(--gray-500); line-height: 1.6; }

/* Stock bar demo */
.stock-bar-demo { margin-top: 24px; display: flex; flex-direction: column; gap: 12px; }
.sb-item { display: flex; align-items: center; gap: 10px; font-size: 12px; color: var(--gray-600); }
.sb-item > span:first-child { width: 110px; flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sb-track { flex: 1; height: 6px; background: var(--gray-100); border-radius: 99px; overflow: hidden; }
.sb-fill { height: 100%; width: var(--w); background: var(--c); border-radius: 99px; animation: sb-fill 1.5s ease forwards; }
.sb-status { padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; white-space: nowrap; }
.sb-status.ok     { background:rgba(16,185,129,.1); color:#10b981; }
.sb-status.warn   { background:rgba(245,158,11,.1); color:#f59e0b; }
.sb-status.danger { background:rgba(239,68,68,.1); color:#ef4444; }

/* Supplier demo */
.supplier-demo { margin-top: 20px; display: flex; flex-direction: column; gap: 10px; }
.sd-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: var(--gray-50); border-radius: 10px; border: 1px solid var(--gray-100); }
.sd-name { font-size: 13px; color: var(--gray-700); font-weight: 500; }
.sd-tag { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
.sd-tag.active  { background:rgba(16,185,129,.1); color:#10b981; }
.sd-tag.pending { background:rgba(245,158,11,.1); color:#f59e0b; }

/* Security rings */
.security-rings {
    position: relative;
    width: 120px; height: 120px;
    margin: 28px auto 0;
}
.sr {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    border: 1.5px solid rgba(20,184,166,.2);
}
.sr-1 { animation: ring-pulse 3s ease-in-out infinite; }
.sr-2 { animation: ring-pulse 3s ease-in-out infinite .5s; inset: 15px; }
.sr-3 { animation: ring-pulse 3s ease-in-out infinite 1s; inset: 30px; }
.sr-icon {
    position: absolute;
    inset: 40px;
    background: rgba(20,184,166,.1);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #14b8a6;
    font-size: 20px;
}

/* ══════════════════════════════════════════════════════════
   STATS
══════════════════════════════════════════════════════════ */
.stats-section {
    padding: 100px 0;
    background: #0f0f1a;
    position: relative;
    overflow: hidden;
}
.stats-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 80% at 50% 50%, rgba(249,115,22,.08), transparent);
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px;
    text-align: center;
    position: relative;
}
.stat-big { position: relative; }
.stat-big::after {
    content: '';
    position: absolute;
    right: 0; top: 20%;
    width: 1px; height: 60%;
    background: rgba(255,255,255,.08);
}
.stat-big:last-child::after { display: none; }
.stat-number {
    display: block;
    font-size: clamp(40px, 5vw, 60px);
    font-weight: 900;
    color: #fff;
    margin-bottom: 8px;
    background: linear-gradient(135deg, #fff, #f97316);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.stat-desc { font-size: 15px; color: rgba(255,255,255,.4); font-weight: 500; }

/* ══════════════════════════════════════════════════════════
   PRICING TEASER
══════════════════════════════════════════════════════════ */
.pricing-teaser { padding: 100px 0; background: #fff; }

.pills-container { display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; margin-bottom: 48px; }

.pill-card {
    background: #fff;
    border: 1.5px solid var(--gray-200);
    border-radius: 20px;
    padding: 28px 24px;
    text-align: center;
    text-decoration: none;
    color: var(--gray-900);
    position: relative;
    min-width: 170px;
    transition: all .4s cubic-bezier(.175,.885,.32,1.275);
    display: flex; flex-direction: column; align-items: center;
}
.pill-card:hover { transform: translateY(-8px); border-color: var(--orange-400); box-shadow: 0 20px 40px rgba(249,115,22,.12); }
.pill-card.popular { border-color: var(--orange-500); box-shadow: 0 8px 30px rgba(249,115,22,.15); }
.pill-card.lifetime { border-color: #8b5cf6; background: linear-gradient(135deg, #faf5ff, #fff); }
.pill-card.lifetime:hover { box-shadow: 0 20px 40px rgba(139,92,246,.15); border-color: #7c3aed; }

.pill-popular {
    position: absolute;
    top: -13px; left: 50%;
    transform: translateX(-50%);
    background: var(--orange-500);
    color: #fff;
    padding: 4px 16px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.pill-popular.lifetime-badge { background: #8b5cf6; }

.pill-name { font-size: 14px; font-weight: 600; color: var(--gray-600); margin-bottom: 8px; }
.pill-price { font-size: 26px; font-weight: 800; color: var(--gray-900); line-height: 1; margin-bottom: 2px; }
.pill-currency { font-size: 11px; color: var(--gray-400); margin-bottom: 4px; }
.pill-period { font-size: 12px; color: var(--gray-400); margin-bottom: 10px; }
.pill-badge { display:inline-block; padding:3px 10px; background:var(--orange-50); color:var(--orange-600); border-radius:20px; font-size:11px; font-weight:700; }

.teaser-cta { text-align: center; }
.small-note { margin-top: 16px; font-size: 13px; color: var(--gray-400); }

/* ══════════════════════════════════════════════════════════
   TESTIMONIALS CAROUSEL
══════════════════════════════════════════════════════════ */
.testimonials-section { padding: 100px 0; background: var(--gray-50); }

.testimonials-carousel { position: relative; overflow: hidden; }
.tc-track {
    display: flex;
    transition: transform .5s cubic-bezier(.25,.46,.45,.94);
}
.tc-item {
    min-width: 100%;
    padding: 0 20px;
    box-sizing: border-box;
}
@media(min-width: 768px) {
    .tc-item { min-width: 50%; }
}
@media(min-width: 1024px) {
    .tc-item { min-width: 33.333%; }
}

.testimonial-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: 24px;
    padding: 32px;
    height: 100%;
    transition: all .3s;
}
.testimonial-card:hover { border-color: var(--orange-200); box-shadow: 0 12px 40px rgba(249,115,22,.08); transform: translateY(-4px); }

.tc-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.tc-stars { color: #f97316; font-size: 18px; letter-spacing: 2px; }
.tc-tag { padding: 4px 12px; background: var(--orange-50); color: var(--orange-600); border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid var(--orange-100); }

.tc-text { font-style: italic; color: var(--gray-600); line-height: 1.7; margin-bottom: 24px; font-size: 14px; }

.tc-author { display: flex; align-items: center; gap: 12px; }
.tc-avatar { width: 44px; height: 44px; background: linear-gradient(135deg, var(--orange-400), var(--orange-600)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0; }
.tc-author strong { display: block; font-size: 14px; color: var(--gray-900); }
.tc-author span { font-size: 12px; color: var(--gray-400); }

.tc-controls { display: flex; align-items: center; justify-content: center; gap: 20px; margin-top: 36px; }
.tc-btn { width: 44px; height: 44px; border: 1.5px solid var(--gray-200); border-radius: 50%; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .3s; color: var(--gray-600); }
.tc-btn:hover { border-color: var(--orange-400); color: var(--orange-500); transform: scale(1.1); }
.tc-dots { display: flex; gap: 8px; }
.tc-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--gray-200); cursor: pointer; transition: all .3s; border: none; padding: 0; }
.tc-dot.active { background: var(--orange-500); transform: scale(1.3); }

/* ══════════════════════════════════════════════════════════
   FAQ ACCORDÉON
══════════════════════════════════════════════════════════ */
.faq-section { padding: 100px 0; background: #fff; }

.faq-accordion { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }

.faq-item {
    background: #fff;
    border: 1.5px solid var(--gray-100);
    border-radius: 16px;
    overflow: hidden;
    transition: border-color .3s, box-shadow .3s;
}
.faq-item.open { border-color: var(--orange-300); box-shadow: 0 8px 24px rgba(249,115,22,.08); }

.faq-question {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px 24px;
    background: none;
    border: none;
    text-align: left;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    color: var(--gray-900);
    transition: color .3s;
}
.faq-item.open .faq-question { color: var(--orange-600); }

.faq-icon {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--gray-100);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: all .3s;
    color: var(--gray-500);
}
.faq-item.open .faq-icon { background: var(--orange-500); color: #fff; rotate: 45deg; }

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height .4s ease, padding .3s ease;
}
.faq-item.open .faq-answer { max-height: 300px; }
.faq-answer p { padding: 0 24px 20px; font-size: 14px; color: var(--gray-600); line-height: 1.7; }
.faq-answer strong { color: var(--orange-600); }

/* ══════════════════════════════════════════════════════════
   FINAL CTA
══════════════════════════════════════════════════════════ */
.final-cta {
    padding: 80px 0;
    background: #0f0f1a;
    position: relative;
    overflow: hidden;
    text-align: center;
}
.cta-bg { position: absolute; inset: 0; }
.cta-orb { position: absolute; border-radius: 50%; filter: blur(80px); animation: orb-move 10s ease-in-out infinite; }
.cta-orb-1 { width:400px; height:400px; background:rgba(249,115,22,.15); top:-100px; left:-100px; }
.cta-orb-2 { width:300px; height:300px; background:rgba(139,92,246,.1); bottom:-80px; right:-80px; animation-delay:5s; }

.cta-inner { position: relative; z-index: 2; max-width: 680px; margin: 0 auto; }
.cta-badge {
    display: inline-block;
    padding: 8px 20px;
    background: rgba(249,115,22,.15);
    border: 1px solid rgba(249,115,22,.3);
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
    color: #f97316;
    margin-bottom: 24px;
}
.cta-inner h2 { font-size: clamp(32px,5vw,52px); font-weight: 800; color: #fff; line-height: 1.2; margin-bottom: 16px; }
.cta-inner > p { font-size: 17px; color: rgba(255,255,255,.6); margin-bottom: 40px; }

.cta-actions { display: flex; gap: 16px; justify-content: center; margin-bottom: 24px; flex-wrap: wrap; }
.btn-white-outline {
    border-color: rgba(255,255,255,.3) !important;
    color: rgba(255,255,255,.9) !important;
    background: rgba(255,255,255,.05) !important;
}
.btn-white-outline:hover { border-color: rgba(255,255,255,.7) !important; background: rgba(255,255,255,.1) !important; }
.btn-white-bg { background: #fff !important; color: var(--orange-600) !important; }
.btn-white-bg:hover { background: var(--orange-50) !important; }
.cta-note { font-size: 13px; color: rgba(255,255,255,.35); }

/* ══════════════════════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════════════════════ */
@media (max-width: 1024px) {
    .hero-grid { grid-template-columns: 1fr; text-align: center; }
    .hero-stats { justify-content: center; }
    .hero-actions { justify-content: center; }
    .hero-visual { display: none; }
    .features-bento { grid-template-columns: repeat(2, 1fr); }
    .bento-wide { grid-column: span 2; }
    .bento-tall { grid-row: auto; }
    .business-types-grid { grid-template-columns: repeat(2, 1fr); }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .features-bento { grid-template-columns: 1fr; }
    .bento-wide { grid-column: span 1; }
    .business-types-grid { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .stat-big::after { display: none; }
    .hero-section { padding: 80px 0 50px; }
    .for-who-section, .features-section, .stats-section, .pricing-teaser,
    .testimonials-section, .faq-section, .final-cta { padding: 64px 0; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ═══════════════════════════
       PARTICLES CANVAS
    ═══════════════════════════ */
    const canvas = document.getElementById('particles-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        const resize = () => { canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight; };
        resize();
        window.addEventListener('resize', resize);

        const particles = Array.from({length: 60}, () => ({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            r: Math.random() * 1.5 + .3,
            vx: (Math.random() - .5) * .3,
            vy: (Math.random() - .5) * .3,
            alpha: Math.random() * .5 + .1,
        }));

        (function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0) p.x = canvas.width;
                if (p.x > canvas.width) p.x = 0;
                if (p.y < 0) p.y = canvas.height;
                if (p.y > canvas.height) p.y = 0;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(249,115,22,${p.alpha})`;
                ctx.fill();
            });
            requestAnimationFrame(animate);
        })();
    }

    /* ═══════════════════════════
       TYPEWRITER
    ═══════════════════════════ */
    const words = ['stock', 'boutique', 'commerce', 'activité'];
    let wi = 0, ci = 0, deleting = false;
    const el = document.getElementById('typed-word');
    if (el) {
        function typeStep() {
            const word = words[wi];
            if (!deleting) {
                ci++;
                el.textContent = word.slice(0, ci);
                if (ci === word.length) { deleting = true; setTimeout(typeStep, 2000); return; }
                setTimeout(typeStep, 100);
            } else {
                ci--;
                el.textContent = word.slice(0, ci);
                if (ci === 0) { deleting = false; wi = (wi + 1) % words.length; }
                setTimeout(typeStep, deleting ? 60 : 120);
            }
        }
        setTimeout(typeStep, 1500);
    }

    /* ═══════════════════════════
       SCROLL REVEAL
    ═══════════════════════════ */
    const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up, .reveal-card');
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const delay = el.dataset.delay ? parseInt(el.dataset.delay) : 0;
                setTimeout(() => el.classList.add('revealed'), delay);
                obs.unobserve(el);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach(el => obs.observe(el));

    /* ═══════════════════════════
       ANIMATED COUNTERS
    ═══════════════════════════ */
    const counters = document.querySelectorAll('.counter');
    const counterObs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.dataset.target);
                const suffix = el.dataset.suffix || '';
                const duration = 2000;
                const start = performance.now();
                function update(now) {
                    const t = Math.min((now - start) / duration, 1);
                    const ease = 1 - Math.pow(1 - t, 3);
                    el.textContent = Math.floor(ease * target) + suffix;
                    if (t < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
                counterObs.unobserve(el);
            }
        });
    }, { threshold: 0.3 });
    counters.forEach(c => counterObs.observe(c));

    /* ═══════════════════════════
       LIVE DASHBOARD DATA
    ═══════════════════════════ */
    const liveSales = document.getElementById('live-sales');
    const liveRevenue = document.getElementById('live-revenue');
    if (liveSales && liveRevenue) {
        let sales = 24, rev = 180500;
        setInterval(() => {
            sales += Math.floor(Math.random() * 2);
            rev   += Math.floor(Math.random() * 3000 + 500);
            liveSales.textContent = sales;
            liveRevenue.textContent = rev.toLocaleString('fr-FR');
        }, 3000);
    }

    /* ═══════════════════════════
       TESTIMONIALS CAROUSEL
    ═══════════════════════════ */
    const track = document.getElementById('tc-track');
    const dotsContainer = document.getElementById('tc-dots');
    if (track && dotsContainer) {
        const items = track.querySelectorAll('.tc-item');
        let perPage = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;
        let current = 0;
        const total = items.length;
        const pages = Math.ceil(total / perPage);

        // Build dots
        for (let i = 0; i < pages; i++) {
            const d = document.createElement('button');
            d.className = 'tc-dot' + (i === 0 ? ' active' : '');
            d.addEventListener('click', () => goTo(i));
            dotsContainer.appendChild(d);
        }

        function goTo(page) {
            current = Math.max(0, Math.min(page, pages - 1));
            track.style.transform = `translateX(-${current * perPage * (100 / perPage)}%)`;
            dotsContainer.querySelectorAll('.tc-dot').forEach((d, i) => d.classList.toggle('active', i === current));
        }

        document.getElementById('tc-prev').addEventListener('click', () => goTo((current - 1 + pages) % pages));
        document.getElementById('tc-next').addEventListener('click', () => goTo((current + 1) % pages));

        // Auto-play
        let autoplay = setInterval(() => goTo((current + 1) % pages), 4000);
        track.addEventListener('mouseenter', () => clearInterval(autoplay));
        track.addEventListener('mouseleave', () => { autoplay = setInterval(() => goTo((current + 1) % pages), 4000); });
    }

    /* ═══════════════════════════
       FAQ ACCORDION
    ═══════════════════════════ */
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq-item');
            const isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        });
    });

    /* ═══════════════════════════
       SMOOTH SCROLL (nav links)
    ═══════════════════════════ */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
});
</script>
@endsection
