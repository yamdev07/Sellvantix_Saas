{{-- resources/views/landing/register.blade.php --}}
@extends('layouts.landing')

@section('title', 'Créer votre entreprise — Sellvantix')

@section('content')
<div class="register-page">
    <div class="container">
        {{-- En-tête de la page --}}
        <div class="register-header-section">
            <h1>Créez votre <span class="text-accent">entreprise</span></h1>
            <p>Commencez votre essai gratuit de 14 jours · Sans carte bancaire</p>
        </div>

        <div class="register-card">
            <div class="register-card-header">
                <h2>Nouvelle entreprise</h2>
                @php
                    $planNames = [
                        'starter'   => 'Formule Starter',
                        'monthly'   => 'Formule Business',
                        'quarterly' => 'Formule Pro Trimestrielle',
                        'semester'  => 'Formule Pro Semestrielle',
                        'yearly'    => 'Formule Annuelle',
                        'lifetime'  => 'Licence à vie',
                    ];
                    $planPrices = [
                        'starter'   => '10 000 FCFA/mois',
                        'monthly'   => '15 000 FCFA/mois',
                        'quarterly' => '39 900 FCFA/3 mois',
                        'semester'  => '79 900 FCFA/6 mois',
                        'yearly'    => '105 000 FCFA/an',
                        'lifetime'  => '300 000 FCFA (paiement unique)',
                    ];
                @endphp
                <span class="plan-badge">{{ $planNames[$plan] ?? 'Formule Business' }}</span>
            </div>

            <div class="register-card-body">
                <form method="POST" action="{{ route('register.tenant') }}" id="registerForm" novalidate>
                    @csrf
                    <input type="hidden" name="plan" value="{{ $plan }}">

                    {{-- Bannière d'erreur globale --}}
                    @if(session('error'))
                        <div class="alert-error" id="globalError">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert-error" id="globalError">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Veuillez corriger les erreurs ci-dessous avant de continuer.</span>
                        </div>
                    @endif

                    {{-- Informations entreprise --}}
                    <div class="section-title">
                        <i class="bi bi-building"></i>
                        Informations de votre entreprise
                    </div>

                    <div class="form-group">
                        <label for="company_name" class="form-label">
                            Nom de l'entreprise <span class="required">*</span>
                        </label>
                        <input type="text"
                               id="company_name"
                               name="company_name"
                               class="form-control @error('company_name') error @enderror"
                               value="{{ old('company_name') }}"
                               placeholder="Ex: Mon Entreprise"
                               maxlength="100"
                               required>
                        <div class="char-hint" id="hint-company_name"></div>
                        @error('company_name')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subdomain" class="form-label">
                            Sous-domaine <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <input type="text"
                                   id="subdomain"
                                   name="subdomain"
                                   class="form-control @error('subdomain') error @enderror"
                                   value="{{ old('subdomain') }}"
                                   placeholder="mon-entreprise"
                                   maxlength="50"
                                   required>
                            <span class="input-group-text">.quincaapp.com</span>
                        </div>
                        <div class="help-text">
                            <i class="bi bi-info-circle"></i>
                            Lettres minuscules, chiffres et tirets uniquement
                        </div>
                        @error('subdomain')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="address" class="form-label">Adresse</label>
                            <input type="text"
                                   id="address"
                                   name="address"
                                   class="form-control"
                                   value="{{ old('address') }}"
                                   placeholder="Adresse de votre entreprise"
                                   maxlength="200">
                        </div>

                        <div class="form-group half">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone') }}"
                                   placeholder="+229 90 42 25 88"
                                   maxlength="20"
                                   inputmode="tel"
                                   pattern="[\+0-9\s\-]+"
                                   title="Chiffres, +, espaces et tirets uniquement">
                        </div>
                    </div>

                    {{-- Informations propriétaire --}}
                    <div class="section-title">
                        <i class="bi bi-person"></i>
                        Vos informations personnelles
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">
                            Nom complet <span class="required">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control @error('name') error @enderror"
                               value="{{ old('name') }}"
                               placeholder="Jean Dupont"
                               maxlength="100"
                               required>
                        @error('name')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email professionnel <span class="required">*</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control @error('email') error @enderror"
                               value="{{ old('email') }}"
                               placeholder="contact@votre-entreprise.com"
                               maxlength="150"
                               required>
                        @error('email')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Résumé de la commande --}}
                    <div class="order-summary">
                        <h3>Récapitulatif de votre commande</h3>
                        
                        <div class="summary-item">
                            <span>Formule choisie</span>
                            <span class="price-value">{{ $planNames[$plan] ?? 'Business' }}</span>
                        </div>
                        
                        <div class="summary-item">
                            <span>Prix</span>
                            <span class="price-value">{{ $planPrices[$plan] ?? '15 000 FCFA/mois' }}</span>
                        </div>
                        
                        <div class="summary-item">
                            <span>Période d'essai</span>
                            <span class="price-value">14 jours offerts</span>
                        </div>
                        
                        <div class="summary-total">
                            <span>Total à payer aujourd'hui</span>
                            <span class="price-value">0 FCFA</span>
                        </div>
                    </div>

                    {{-- Note sur le mot de passe --}}
                    <div class="password-note">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>
                            <strong>🔐 Mot de passe généré automatiquement</strong>
                            <p>Un mot de passe sécurisé sera généré et envoyé à votre adresse email. Vous pourrez le modifier dans votre espace.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="bi bi-rocket-takeoff"></i>
                        Créer mon compte et commencer l'essai gratuit
                    </button>

                    <div class="terms-note">
                        En créant votre compte, vous acceptez nos
                        <a href="#" onclick="openModal('modal-terms'); return false;">conditions d'utilisation</a> et
                        <a href="#" onclick="openModal('modal-privacy'); return false;">politique de confidentialité</a>.
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* =====================================================
   PAGE D'INSCRIPTION - DESIGN COHÉRENT AVEC LE LAYOUT
===================================================== */
.register-page {
    padding: 60px 0;
    background: var(--bg-body);
    min-height: calc(100vh - 400px);
}

.register-header-section {
    text-align: center;
    margin-bottom: 48px;
}

.register-header-section h1 {
    font-size: 36px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.register-header-section p {
    font-size: 16px;
    color: var(--text-secondary);
}

.register-card {
    max-width: 700px;
    margin: 0 auto;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    transition: border-color 0.3s;
}

.register-card:hover {
    border-color: var(--accent);
}

.register-card-header {
    background: var(--accent-gradient);
    padding: 32px 40px;
    color: white;
    text-align: center;
    position: relative;
}

.register-card-header h2 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 16px;
}

.plan-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 8px 24px;
    border-radius: 40px;
    font-size: 14px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.3);
}

.register-card-body {
    padding: 40px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 32px 0 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    padding-left: 12px;
}

.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 2px;
    bottom: 2px;
    width: 4px;
    background: var(--accent-gradient);
    border-radius: 4px;
}

.section-title:first-of-type {
    margin-top: 0;
}

.section-title i {
    color: var(--accent);
    font-size: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group.half {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 6px;
    letter-spacing: 0.3px;
}

.form-label .required {
    color: #dc2626;
    margin-left: 4px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    background: var(--gray-50);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 15px;
    color: var(--text-primary);
    transition: all 0.2s;
}

.form-control:focus {
    border-color: var(--accent);
    outline: none;
    box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    background: var(--white);
}

.form-control.error {
    border-color: #dc2626;
    background: #fef2f2;
}

.input-group {
    display: flex;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    overflow: hidden;
    background: var(--gray-50);
}

.input-group .form-control {
    border: none;
    background: transparent;
}

.input-group .form-control:focus {
    box-shadow: none;
}

.input-group-text {
    padding: 0 16px;
    background: var(--gray-100);
    color: var(--text-secondary);
    font-size: 14px;
    display: flex;
    align-items: center;
    border-left: 1.5px solid var(--border);
}

.help-text {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.help-text i {
    color: var(--accent);
}

.error-message {
    font-size: 12px;
    color: #dc2626;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.order-summary {
    background: var(--gray-50);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 28px;
    margin: 32px 0;
}

.order-summary h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 20px;
    position: relative;
    padding-left: 12px;
}

.order-summary h3::before {
    content: '';
    position: absolute;
    left: 0;
    top: 2px;
    bottom: 2px;
    width: 4px;
    background: var(--accent-gradient);
    border-radius: 4px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
    font-size: 15px;
    color: var(--text-secondary);
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 16px 0 0;
    margin-top: 8px;
    font-weight: 700;
    font-size: 18px;
    color: var(--text-primary);
    border-top: 2px solid var(--accent-soft);
}

.price-value {
    color: var(--accent);
    font-weight: 700;
}

.password-note {
    background: var(--accent-light);
    border: 1px solid var(--accent-soft);
    border-radius: var(--radius-sm);
    padding: 16px 20px;
    margin-bottom: 28px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.password-note i {
    color: var(--accent);
    font-size: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}

.password-note strong {
    display: block;
    font-size: 14px;
    color: var(--accent);
    margin-bottom: 4px;
}

.password-note p {
    font-size: 13px;
    color: var(--text-secondary);
    line-height: 1.5;
}

.btn-submit {
    width: 100%;
    padding: 16px 24px;
    background: var(--accent-gradient);
    border: none;
    border-radius: var(--radius-sm);
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: var(--shadow-orange);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px -8px rgba(249,115,22,0.4);
}

.btn-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.alert-error {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fef2f2;
    border: 1px solid #fca5a5;
    color: #991b1b;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}
.alert-error i { font-size: 18px; flex-shrink: 0; }
.char-hint {
    font-size: 11px;
    color: #94a3b8;
    text-align: right;
    margin-top: 4px;
}
.char-hint.warn { color: #f97316; }
.char-hint.danger { color: #ef4444; font-weight: 600; }

.terms-note {
    text-align: center;
    margin-top: 24px;
    font-size: 13px;
    color: var(--text-tertiary);
}

.terms-note a {
    color: var(--accent);
    text-decoration: none;
    font-weight: 500;
}

.terms-note a:hover {
    text-decoration: underline;
}

/* Animation de chargement */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .register-page {
        padding: 40px 0;
    }

    .register-header-section h1 {
        font-size: 28px;
    }

    .register-card-header {
        padding: 28px 20px;
    }

    .register-card-body {
        padding: 28px 20px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .order-summary {
        padding: 20px;
    }

    .input-group {
        flex-direction: column;
    }

    .input-group-text {
        border-left: none;
        border-top: 1.5px solid var(--border);
        justify-content: center;
        padding: 10px;
    }
}

@media (max-width: 480px) {
    .register-header-section h1 {
        font-size: 24px;
    }

    .register-card-header h2 {
        font-size: 20px;
    }

    .plan-badge {
        padding: 6px 16px;
        font-size: 12px;
    }

    .summary-item {
        font-size: 14px;
        flex-direction: column;
        gap: 4px;
        text-align: center;
    }

    .summary-total {
        font-size: 16px;
        flex-direction: column;
        gap: 4px;
        text-align: center;
    }

    .password-note {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
// Scroll to first error or global alert on page load
window.addEventListener('DOMContentLoaded', function () {
    const firstError = document.getElementById('globalError')
        || document.querySelector('.error-message')
        || document.querySelector('.error');
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Character counter for inputs with maxlength
document.querySelectorAll('input[maxlength]').forEach(function (input) {
    const hintId = 'hint-' + input.name;
    const hint = document.getElementById(hintId);
    if (!hint) return;
    const max = parseInt(input.maxLength);
    function update() {
        const left = max - input.value.length;
        hint.textContent = left + ' / ' + max + ' caractères restants';
        hint.className = 'char-hint' + (left <= 10 ? ' danger' : left <= 20 ? ' warn' : '');
    }
    input.addEventListener('input', update);
    update();
});

// Subdomain: auto-format
const subdomainInput = document.querySelector('input[name="subdomain"]');
if (subdomainInput) {
    subdomainInput.addEventListener('input', function () {
        this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
    });
}

// Phone: digits, +, spaces and dashes only
const phoneInput = document.querySelector('input[name="phone"]');
if (phoneInput) {
    phoneInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9\+\s\-]/g, '');
    });
}

// Submit: validate first, then disable button
document.getElementById('registerForm')?.addEventListener('submit', function (e) {
    const required = this.querySelectorAll('[required]');
    let valid = true;
    required.forEach(function (field) {
        if (!field.value.trim()) { valid = false; field.classList.add('error'); }
        else { field.classList.remove('error'); }
    });
    if (!valid) {
        e.preventDefault();
        const firstInvalid = this.querySelector('.error');
        if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite"></i> Création en cours...';
    submitBtn.disabled = true;
});

function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['modal-terms', 'modal-privacy'].forEach(closeModal);
    }
});
</script>

<!-- Modal Conditions d'utilisation -->
<div id="modal-terms" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.6);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)closeModal('modal-terms')">
    <div style="background:#fff;border-radius:20px;max-width:640px;width:100%;max-height:80vh;overflow-y:auto;padding:40px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h2 style="font-size:22px;font-weight:700;color:#0f172a;margin:0">Conditions d'utilisation</h2>
            <button onclick="closeModal('modal-terms')" style="background:none;border:none;font-size:24px;cursor:pointer;color:#64748b;line-height:1">&times;</button>
        </div>
        <div style="font-size:14px;line-height:1.8;color:#334155;">
            <p><strong>Dernière mise à jour :</strong> Juin 2026</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">1. Acceptation des conditions</h3>
            <p>En accédant à Sellvantix, vous acceptez d'être lié par les présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre service.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">2. Description du service</h3>
            <p>Sellvantix est une plateforme SaaS de gestion de stock, de ventes et de clients destinée aux commerces de détail en Afrique de l'Ouest. Nous offrons une période d'essai gratuite de 14 jours sans engagement.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">3. Compte utilisateur</h3>
            <p>Vous êtes responsable de la confidentialité de vos identifiants de connexion et de toutes les activités réalisées sous votre compte. Vous devez nous notifier immédiatement de toute utilisation non autorisée.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">4. Abonnement et paiement</h3>
            <p>Les abonnements sont facturés mensuellement ou annuellement selon le plan choisi. Le renouvellement est automatique sauf résiliation explicite avant la date de renouvellement.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">5. Données</h3>
            <p>Vos données restent votre propriété. Nous ne les cédons pas à des tiers. En cas de résiliation, vos données sont exportables pendant 30 jours puis supprimées.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">6. Contact</h3>
            <p>Pour toute question : <a href="mailto:contact@yyamd.com" style="color:#f97316">contact@yyamd.com</a> ou WhatsApp : +229 90 42 25 88</p>
        </div>
        <div style="margin-top:28px;text-align:right;">
            <button onclick="closeModal('modal-terms')" style="background:#f97316;color:#fff;border:none;padding:10px 28px;border-radius:40px;font-weight:600;cursor:pointer;font-size:14px;">J'ai compris</button>
        </div>
    </div>
</div>

<!-- Modal Politique de confidentialité -->
<div id="modal-privacy" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.6);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)closeModal('modal-privacy')">
    <div style="background:#fff;border-radius:20px;max-width:640px;width:100%;max-height:80vh;overflow-y:auto;padding:40px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h2 style="font-size:22px;font-weight:700;color:#0f172a;margin:0">Politique de confidentialité</h2>
            <button onclick="closeModal('modal-privacy')" style="background:none;border:none;font-size:24px;cursor:pointer;color:#64748b;line-height:1">&times;</button>
        </div>
        <div style="font-size:14px;line-height:1.8;color:#334155;">
            <p><strong>Dernière mise à jour :</strong> Juin 2026</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">1. Données collectées</h3>
            <p>Nous collectons les informations que vous fournissez lors de la création de votre compte (nom, email, nom de l'entreprise) ainsi que les données d'utilisation du service (ventes, stocks, clients).</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">2. Utilisation des données</h3>
            <p>Vos données sont utilisées uniquement pour fournir et améliorer le service Sellvantix. Elles ne sont jamais vendues ni partagées avec des tiers à des fins commerciales.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">3. Sécurité</h3>
            <p>Vos données sont chiffrées en transit (HTTPS) et au repos. L'accès est strictement limité aux membres de notre équipe ayant besoin d'en connaître.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">4. Cookies</h3>
            <p>Nous utilisons des cookies essentiels pour maintenir votre session. Aucun cookie publicitaire ou de tracking tiers n'est utilisé.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">5. Vos droits</h3>
            <p>Vous pouvez à tout moment demander l'accès, la modification ou la suppression de vos données en nous contactant à <a href="mailto:contact@yyamd.com" style="color:#f97316">contact@yyamd.com</a>.</p>

            <h3 style="font-size:16px;font-weight:600;margin:20px 0 8px;color:#0f172a">6. Conservation</h3>
            <p>Les données sont conservées pendant toute la durée de votre abonnement et 30 jours après la résiliation pour permettre l'export.</p>
        </div>
        <div style="margin-top:28px;text-align:right;">
            <button onclick="closeModal('modal-privacy')" style="background:#f97316;color:#fff;border:none;padding:10px 28px;border-radius:40px;font-weight:600;cursor:pointer;font-size:14px;">J'ai compris</button>
        </div>
    </div>
</div>

@endsection