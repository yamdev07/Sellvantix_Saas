<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

@push('styles')
<style>
    /* Reset complet pour cette page */
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100% !important;
        overflow: auto !important;
        background: #0c0c0f !important;
    }
    body > div {
        min-height: 100vh;
        display: block !important;
        flex-direction: unset !important;
    }

    .qapp-page {
        min-height: 100vh;
        display: flex;
        background: #0c0c0f;
        font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
    }

    /* ══════════════════════════════
       PANNEAU GAUCHE
    ══════════════════════════════ */
    .qapp-left {
        width: 50%;
        display: none;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 56px;
        background: #111116;
        position: relative;
        overflow: hidden;
        border-right: 1px solid rgba(249,115,22,0.1);
    }
    @media (min-width: 1024px) {
        .qapp-left { display: flex; }
    }

    .qapp-grid-bg {
        position: absolute; inset: 0; pointer-events: none;
        background-image:
            linear-gradient(rgba(249,115,22,0.055) 1px, transparent 1px),
            linear-gradient(90deg, rgba(249,115,22,0.055) 1px, transparent 1px);
        background-size: 48px 48px;
    }
    .qapp-orb {
        position: absolute; border-radius: 50%;
        pointer-events: none; filter: blur(90px);
    }
    .qapp-orb-a { width: 400px; height: 400px; top: -100px; right: -80px; background: rgba(249,115,22,0.1); }
    .qapp-orb-b { width: 280px; height: 280px; bottom: 40px; left: -50px; background: rgba(249,115,22,0.06); }

    /* Logo */
    .qapp-logo { display: flex; align-items: center; gap: 13px; position: relative; z-index: 2; }
    .qapp-logo-hex {
        width: 46px; height: 46px; flex-shrink: 0;
        background: linear-gradient(135deg, #f97316, #ea580c);
        clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 28px rgba(249,115,22,0.45);
    }
    .qapp-logo-hex svg { width: 22px; height: 22px; stroke: #fff; fill: none; }
    .qapp-logo-name { font-size: 20px; font-weight: 700; color: #f5f3ef; letter-spacing: -0.3px; line-height: 1.1; }
    .qapp-logo-name em { font-style: normal; color: #f97316; }
    .qapp-logo-tag { font-size: 10px; color: #4e4c5a; letter-spacing: 2.5px; text-transform: uppercase; margin-top: 2px; }

    /* Hero */
    .qapp-hero { position: relative; z-index: 2; margin-top: 44px; }
    .qapp-eyebrow {
        display: inline-flex; align-items: center; gap: 9px;
        font-size: 11px; font-weight: 600; letter-spacing: 2.2px;
        text-transform: uppercase; color: #f97316; margin-bottom: 22px;
    }
    .qapp-eyebrow::before {
        content: ''; display: block;
        width: 26px; height: 1.5px;
        background: #f97316; border-radius: 2px;
    }
    .qapp-hero h1 {
        font-size: clamp(28px, 2.9vw, 44px);
        font-weight: 800; line-height: 1.1;
        color: #f5f3ef; letter-spacing: -1px;
        margin: 0 0 18px;
    }
    .qapp-hero h1 .qapp-grad {
        background: linear-gradient(118deg, #f97316 0%, #fb923c 55%, #fbbf24 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .qapp-hero-desc {
        font-size: 15px; color: #55535f;
        line-height: 1.75; max-width: 390px; margin: 0;
    }

    /* Feature cards */
    .qapp-feats {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 10px; margin-top: 38px;
        position: relative; z-index: 2;
    }
    .qapp-feat {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 15px 16px;
        background: rgba(255,255,255,0.025);
        border: 1px solid rgba(249,115,22,0.1);
        border-radius: 14px;
        transition: border-color 0.2s, background 0.2s;
        cursor: default;
    }
    .qapp-feat:hover {
        border-color: rgba(249,115,22,0.28);
        background: rgba(249,115,22,0.045);
    }
    .qapp-feat-ico {
        width: 36px; height: 36px; flex-shrink: 0;
        border-radius: 10px; background: rgba(249,115,22,0.13);
        display: flex; align-items: center; justify-content: center;
    }
    .qapp-feat-ico svg { width: 17px; height: 17px; stroke: #f97316; fill: none; }
    .qapp-feat-title { font-size: 13px; font-weight: 600; color: #ddd9d3; line-height: 1.3; }
    .qapp-feat-desc { font-size: 11px; color: #55535f; margin-top: 2px; }

    /* Stats */
    .qapp-stats {
        display: flex; align-items: center;
        padding-top: 30px;
        border-top: 1px solid rgba(249,115,22,0.12);
        position: relative; z-index: 2;
    }
    .qapp-stat { flex: 1; text-align: center; }
    .qapp-stat-n { font-size: 23px; font-weight: 700; color: #f5f3ef; line-height: 1; }
    .qapp-stat-n em { font-style: normal; color: #f97316; }
    .qapp-stat-l { font-size: 11px; color: #55535f; margin-top: 3px; }
    .qapp-stat-sep { width: 1px; height: 38px; background: rgba(249,115,22,0.12); }

    /* ══════════════════════════════
       PANNEAU DROIT
    ══════════════════════════════ */
    .qapp-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        background: #0c0c0f;
    }

    .qapp-card {
        width: 100%; max-width: 400px;
        background: #111116;
        border: 1px solid rgba(249,115,22,0.13);
        border-radius: 22px;
        padding: 38px 34px;
        position: relative;
        animation: qappCardIn 0.5s cubic-bezier(0.16,1,0.3,1) both;
    }
    .qapp-card::before {
        content: '';
        position: absolute; top: -1px; left: 22%; right: 22%;
        height: 1px;
        background: linear-gradient(90deg, transparent, #f97316, transparent);
    }
    @keyframes qappCardIn {
        from { opacity: 0; transform: translateY(28px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Mobile-only logo */
    .qapp-mobile-logo {
        display: flex; align-items: center;
        gap: 10px; justify-content: center;
        margin-bottom: 30px;
    }
    @media (min-width: 1024px) { .qapp-mobile-logo { display: none; } }

    /* Card head */
    .qapp-card-head { text-align: center; margin-bottom: 30px; }
    .qapp-pill {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(249,115,22,0.1);
        border: 1px solid rgba(249,115,22,0.22);
        border-radius: 100px; padding: 5px 13px;
        font-size: 11px; font-weight: 600; color: #fb923c;
        letter-spacing: 0.4px; margin-bottom: 16px;
    }
    .qapp-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #f97316;
        animation: qappBlink 1.8s ease-in-out infinite;
    }
    @keyframes qappBlink { 0%,100%{opacity:1;} 50%{opacity:0.2;} }
    .qapp-card-head h2 {
        font-size: 25px; font-weight: 700;
        color: #f5f3ef; letter-spacing: -0.4px;
        margin: 0 0 5px;
    }
    .qapp-card-head p { font-size: 14px; color: #55535f; margin: 0; }

    /* Fields */
    .qapp-field { margin-bottom: 17px; }
    .qapp-field > label {
        display: block; font-size: 11.5px; font-weight: 600;
        color: #8a87a0; letter-spacing: 0.9px;
        text-transform: uppercase; margin-bottom: 8px;
    }
    .qapp-field-wrap { position: relative; display: flex; align-items: center; }
    .qapp-field-ico {
        position: absolute; left: 13px;
        pointer-events: none; display: flex; align-items: center;
    }
    .qapp-field-ico svg { width: 16px; height: 16px; stroke: #3a3848; fill: none; transition: stroke 0.2s; }
    .qapp-field-wrap:focus-within .qapp-field-ico svg { stroke: #f97316; }
    .qapp-field-wrap input {
        width: 100%;
        padding: 12px 14px 12px 42px;
        background: #17171e;
        border: 1.5px solid #28263380;
        border-radius: 11px;
        font-size: 14px; color: #e5e2dd;
        font-family: inherit; outline: none;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        -webkit-appearance: none; appearance: none;
        box-sizing: border-box;
    }
    .qapp-field-wrap input::placeholder { color: #38364680; }
    .qapp-field-wrap input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3.5px rgba(249,115,22,0.13);
        background: #1b1b24;
    }
    .qapp-field-err { font-size: 12px; color: #f87171; margin-top: 5px; }

    /* Toggle afficher/masquer mot de passe */
    .qapp-field-wrap--pass input { padding-right: 44px; }
    .qapp-field-toggle {
        position: absolute; right: 8px;
        display: flex; align-items: center; justify-content: center;
        width: 30px; height: 30px;
        background: transparent; border: none; padding: 0;
        cursor: pointer; border-radius: 8px;
        transition: background 0.15s;
    }
    .qapp-field-toggle:hover { background: rgba(249,115,22,0.1); }
    .qapp-field-toggle svg { width: 17px; height: 17px; stroke: #55535f; fill: none; transition: stroke 0.15s; }
    .qapp-field-toggle:hover svg { stroke: #f97316; }
    .qapp-field-toggle .qapp-eye-off { display: none; }
    .qapp-field-toggle.is-visible .qapp-eye-on { display: none; }
    .qapp-field-toggle.is-visible .qapp-eye-off { display: block; }

    /* Options row */
    .qapp-row {
        display: flex; align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
    }
    .qapp-check {
        display: flex; align-items: center;
        gap: 8px; cursor: pointer;
    }
    .qapp-check input[type=checkbox] {
        width: 15px; height: 15px;
        accent-color: #f97316; cursor: pointer; flex-shrink: 0;
    }
    .qapp-check span { font-size: 13px; color: #55535f; }
    .qapp-forgot {
        font-size: 13px; color: #f97316;
        text-decoration: none; font-weight: 500;
        transition: color 0.15s;
    }
    .qapp-forgot:hover { color: #fb923c; }

    /* Button */
    .qapp-btn {
        width: 100%; padding: 13.5px;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        border: none; border-radius: 11px;
        font-family: inherit; font-size: 15px;
        font-weight: 700; color: #fff;
        cursor: pointer; position: relative; overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex; align-items: center;
        justify-content: center; gap: 9px;
        letter-spacing: 0.1px; box-sizing: border-box;
    }
    .qapp-btn svg { width: 16px; height: 16px; stroke: white; fill: none; flex-shrink: 0; }
    .qapp-btn::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transform: translateX(-100%);
        transition: transform 0.55s ease;
    }
    .qapp-btn:hover::after { transform: translateX(100%); }
    .qapp-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 40px -8px rgba(249,115,22,0.5);
    }
    .qapp-btn:active { transform: none; box-shadow: none; }

    /* Divider */
    .qapp-divider {
        display: flex; align-items: center;
        gap: 10px; margin: 22px 0;
    }
    .qapp-divider::before,
    .qapp-divider::after { content: ''; flex: 1; height: 1px; background: #25233080; }
    .qapp-divider span { font-size: 11px; color: #3e3c4c; letter-spacing: 1px; }

    /* Register */
    .qapp-reg { text-align: center; font-size: 13px; color: #55535f; }
    .qapp-reg a {
        color: #f97316; font-weight: 600;
        text-decoration: none; transition: color 0.15s;
    }
    .qapp-reg a:hover { color: #fb923c; }

    /* Security */
    .qapp-sec {
        display: flex; align-items: center;
        justify-content: center; gap: 6px;
        margin-top: 18px; font-size: 11px; color: #38364680;
    }
    .qapp-sec svg { width: 12px; height: 12px; stroke: currentColor; fill: none; }

    /* Session alert */
    .qapp-alert {
        display: flex; align-items: center; gap: 9px;
        background: rgba(249,115,22,0.07);
        border: 1px solid rgba(249,115,22,0.2);
        border-radius: 10px; padding: 10px 14px;
        margin-bottom: 16px; font-size: 13px; color: #fb923c;
    }
    .qapp-alert svg { width: 15px; height: 15px; stroke: currentColor; fill: none; flex-shrink: 0; }
</style>
@endpush

<div class="qapp-page">

    {{-- ══════════════════════════
         PANNEAU GAUCHE
    ══════════════════════════ --}}
    <div class="qapp-left">
        <div class="qapp-grid-bg"></div>
        <div class="qapp-orb qapp-orb-a"></div>
        <div class="qapp-orb qapp-orb-b"></div>

        {{-- Logo --}}
        <div class="qapp-logo">
            <div class="qapp-logo-hex">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                </svg>
            </div>
            <div>
                <div class="qapp-logo-name">Sellvantix</div>
                <div class="qapp-logo-tag">Gestion de stock</div>
            </div>
        </div>

        {{-- Hero --}}
        <div class="qapp-hero">
            <div class="qapp-eyebrow">Plateforme Pro</div>
            <h1>
                Pilotez votre<br>
                entreprise<br>
                avec <span class="qapp-grad">puissance.</span>
            </h1>
            <p class="qapp-hero-desc">
                Stocks, ventes, équipes et analytics — tout centralisé
                dans une interface forgée pour la performance.
            </p>

            {{-- Features --}}
            <div class="qapp-feats">
                @php
                    $features = [
                        ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'Sécurisé', 'Chiffrement AES-256'],
                        ['M13 10V3L4 14h7v7l9-11h-7z', 'Ultra-rapide', '< 100ms réponse'],
                        ['M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'Analytics', 'Données temps réel'],
                        ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'Équipes', 'Rôles & permissions'],
                    ];
                @endphp
                @foreach($features as $f)
                <div class="qapp-feat">
                    <div class="qapp-feat-ico">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $f[0] }}"/>
                        </svg>
                    </div>
                    <div>
                        <div class="qapp-feat-title">{{ $f[1] }}</div>
                        <div class="qapp-feat-desc">{{ $f[2] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stats --}}
        <div class="qapp-stats">
            <div class="qapp-stat">
                <div class="qapp-stat-n">500<em>+</em></div>
                <div class="qapp-stat-l">Clients actifs</div>
            </div>
            <div class="qapp-stat-sep"></div>
            <div class="qapp-stat">
                <div class="qapp-stat-n">99.9<em>%</em></div>
                <div class="qapp-stat-l">Disponibilité</div>
            </div>
            <div class="qapp-stat-sep"></div>
            <div class="qapp-stat">
                <div class="qapp-stat-n">24<em>/7</em></div>
                <div class="qapp-stat-l">Support</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════
         PANNEAU DROIT
    ══════════════════════════ --}}
    <div class="qapp-right">
        <div class="qapp-card">

            {{-- Logo mobile uniquement --}}
            <div class="qapp-mobile-logo">
                <div class="qapp-logo-hex" style="width:38px;height:38px;">
                    <svg viewBox="0 0 24 24" stroke-width="2"
                         style="width:18px;height:18px;stroke:white;fill:none;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                    </svg>
                </div>
                <div class="qapp-logo-name">Sellvantix</div>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="qapp-alert">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Header --}}
            <div class="qapp-card-head">
                <div class="qapp-pill">
                    <span class="qapp-dot"></span>
                    Connexion sécurisée
                </div>
                <h2>Bon retour 👋</h2>
                <p>Accédez à votre espace de gestion</p>
            </div>

            <form wire:submit="login">

                {{-- Email --}}
                <div class="qapp-field">
                    <label for="email">Adresse email</label>
                    <div class="qapp-field-wrap">
                        <span class="qapp-field-ico">
                            <svg viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input
                            wire:model="form.email"
                            type="email"
                            id="email"
                            name="email"
                            placeholder="nom@entreprise.com"
                            autocomplete="email"
                            required
                        >
                    </div>
                    @error('form.email')
                        <p class="qapp-field-err">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="qapp-field">
                    <label for="password">Mot de passe</label>
                    <div class="qapp-field-wrap qapp-field-wrap--pass">
                        <span class="qapp-field-ico">
                            <svg viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input
                            wire:model="form.password"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="qapp-field-toggle"
                                aria-label="Afficher le mot de passe"
                                onclick="
                                    const i = document.getElementById('password');
                                    const show = i.type === 'password';
                                    i.type = show ? 'text' : 'password';
                                    this.classList.toggle('is-visible', show);
                                    this.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
                                ">
                            {{-- Œil ouvert --}}
                            <svg class="qapp-eye-on" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{-- Œil barré --}}
                            <svg class="qapp-eye-off" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                    @error('form.password')
                        <p class="qapp-field-err">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="qapp-row">
                    <label class="qapp-check">
                        <input type="checkbox" wire:model="form.remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate class="qapp-forgot">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="qapp-btn">
                    Se connecter
                    <svg viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>

            </form>

            <div class="qapp-divider"><span>ou</span></div>

            <div class="qapp-sec">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Connexion chiffrée SSL 256-bit
            </div>

        </div>
    </div>

</div>