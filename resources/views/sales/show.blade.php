@extends('layouts.app')

@section('title', 'Détails de la vente — Sellvantix')

@section('styles')
<style>
    :root {
        --orange:        #f97316;
        --orange-dark:   #ea580c;
        --orange-pale:   #fff7ed;
        --orange-soft:   #fed7aa;
        --bg:            #f1f5f9;
        --card:          #ffffff;
        --border:        #e2e8f0;
        --border-light:  #f1f5f9;
        --text:          #0f172a;
        --text-2:        #475569;
        --text-3:        #94a3b8;
        --success:       #16a34a;
        --danger:        #dc2626;
        --info:          #2563eb;
        --purple:        #7c3aed;
        --shadow-sm:     0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
        --shadow-md:     0 4px 16px rgba(15,23,42,.08);
        --shadow-orange: 0 8px 24px rgba(249,115,22,.25);
        --radius:        20px;
        --radius-sm:     12px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: var(--bg);
        color: var(--text);
        -webkit-font-smoothing: antialiased;
    }

    /* Animations */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Page */
    .sd-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 32px 24px 64px;
    }

    /* Header */
    .sd-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
        animation: fadeUp 0.35s ease both;
    }

    .sd-header-l {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .sd-hex {
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
    .sd-hex svg {
        width: 22px;
        height: 22px;
        stroke: #fff;
        fill: none;
    }

    .sd-title {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: var(--text);
    }
    .sd-title span {
        color: var(--orange);
        font-weight: 800;
    }
    .sd-sub {
        font-size: 13px;
        color: var(--text-3);
        margin-top: 4px;
    }

    /* Boutons */
    .btn-primary, .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 40px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }
    .btn-primary {
        background: linear-gradient(135deg, var(--orange), var(--orange-dark));
        color: #fff;
        box-shadow: var(--shadow-orange);
    }
    .btn-primary::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transform: translateX(-100%);
        transition: transform 0.5s;
    }
    .btn-primary:hover::after { transform: translateX(100%); }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(249,115,22,0.4);
    }
    .btn-secondary {
        background: var(--card);
        border: 1.5px solid var(--border);
        color: var(--text-2);
    }
    .btn-secondary:hover {
        border-color: var(--orange);
        color: var(--orange);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }
    .btn-primary svg, .btn-secondary svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
    }

    /* Stats Cards */
    .sd-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 700px) {
        .sd-stats { grid-template-columns: 1fr; }
    }

    .sd-stat {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 22px;
        box-shadow: var(--shadow-sm);
        animation: fadeUp 0.35s ease both;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .sd-stat:nth-child(2) { animation-delay: 0.07s; }
    .sd-stat:nth-child(3) { animation-delay: 0.14s; }
    .sd-stat:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--orange-soft);
    }
    .sd-stat::before {
        content: '';
        position: absolute;
        top: 14px;
        bottom: 14px;
        left: 0;
        width: 4px;
        border-radius: 0 4px 4px 0;
    }
    .sd-stat.c-a::before { background: var(--info); }
    .sd-stat.c-b::before { background: var(--success); }
    .sd-stat.c-c::before { background: var(--purple); }

    .sd-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .sd-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-3);
    }
    .sd-stat-ico {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .c-a .sd-stat-ico { background: #eff6ff; color: var(--info); }
    .c-b .sd-stat-ico { background: #f0fdf4; color: var(--success); }
    .c-c .sd-stat-ico { background: #f5f3ff; color: var(--purple); }
    .sd-stat:hover .sd-stat-ico {
        background: var(--orange-pale);
        color: var(--orange);
    }
    .sd-stat-ico svg {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        fill: none;
    }
    .sd-stat-val {
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -0.5px;
        line-height: 1;
        margin-bottom: 4px;
        color: var(--text);
    }
    .sd-stat-unit {
        font-size: 12px;
        color: var(--text-3);
        font-weight: 500;
    }

    /* Cards */
    .sd-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        margin-bottom: 20px;
        overflow: hidden;
        animation: fadeUp 0.35s 0.1s ease both;
        transition: border-color 0.2s;
    }
    .sd-card:hover {
        border-color: var(--orange-soft);
    }

    .sd-card-hd {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-light);
        background: #fafbfd;
    }
    .sd-card-hd-l {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sd-card-ico {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: var(--orange-pale);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sd-card-ico svg {
        width: 16px;
        height: 16px;
        stroke: var(--orange);
        fill: none;
    }
    .sd-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }
    .sd-card-badge {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-3);
        background: #f1f5f9;
        border-radius: 100px;
        padding: 4px 12px;
    }
    .sd-card-body {
        padding: 24px;
    }

    /* Info grid */
    .sd-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 700px) {
        .sd-info-grid { grid-template-columns: 1fr; }
    }

    .sd-person {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .sd-avatar {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }
    .av-client { background: linear-gradient(135deg, #60a5fa, #2563eb); }
    .av-cashier { background: linear-gradient(135deg, #a78bfa, #7c3aed); }

    .sd-person-info {
        flex: 1;
    }
    .sd-person-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }
    .sd-person-detail {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        color: var(--text-2);
        margin-bottom: 3px;
    }
    .sd-person-detail svg {
        width: 14px;
        height: 14px;
        stroke: var(--text-3);
        fill: none;
    }

    .sd-empty-state {
        text-align: center;
        padding: 32px;
        background: #fafbfd;
        border-radius: var(--radius-sm);
        border: 1px dashed var(--border);
    }
    .sd-empty-ico {
        width: 56px;
        height: 56px;
        background: var(--border-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }
    .sd-empty-ico svg {
        width: 28px;
        height: 28px;
        stroke: var(--text-3);
        fill: none;
    }
    .sd-empty-text {
        font-size: 14px;
        color: var(--text-2);
    }

    /* Tableau produits */
    .sd-table-wrap {
        overflow-x: auto;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-light);
    }
    .sd-table {
        width: 100%;
        border-collapse: collapse;
    }
    .sd-table thead th {
        padding: 14px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-2);
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
    }
    .sd-table tbody td {
        padding: 16px 20px;
        font-size: 13px;
        color: var(--text-2);
        border-bottom: 1px solid var(--border-light);
        vertical-align: middle;
    }
    .sd-table tbody tr:last-child td {
        border-bottom: none;
    }
    .sd-table tbody tr {
        transition: background 0.15s;
    }
    .sd-table tbody tr:hover td {
        background: var(--orange-pale);
    }

    .sd-product {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .sd-product-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
    }
    .sd-product-info {
        flex: 1;
    }
    .sd-product-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 2px;
    }
    .sd-product-ref {
        font-size: 11px;
        color: var(--text-3);
    }

    .sd-qty-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f1f5f9;
        color: var(--text-2);
        font-weight: 700;
        font-size: 14px;
        transition: all 0.15s;
    }
    tr:hover .sd-qty-badge {
        background: var(--orange-pale);
        color: var(--orange);
    }

    .sd-price {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
    }
    .sd-total {
        font-size: 16px;
        font-weight: 700;
        color: var(--orange);
    }

    .sd-table tfoot td {
        padding: 16px 20px;
        background: #f8fafc;
        border-top: 2px solid var(--border);
    }
    .sd-grand-total {
        font-size: 20px;
        font-weight: 800;
        color: var(--text);
    }
    .sd-grand-total span {
        color: var(--orange);
        font-size: 18px;
        font-weight: 600;
        margin-left: 4px;
    }

    /* Footer actions */
    .sd-footer {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        animation: fadeUp 0.35s 0.2s ease both;
    }
    .sd-footer-left {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-2);
    }
    .sd-footer-left svg {
        width: 16px;
        height: 16px;
        stroke: var(--orange);
        fill: none;
    }

    .btn-danger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        border-radius: 40px;
        font-size: 14px;
        font-weight: 600;
        color: var(--danger);
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-danger svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
    }
    .btn-danger:hover {
        background: var(--danger);
        border-color: var(--danger);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220,38,38,0.25);
    }
</style>
@endsection

@section('content')
<div class="sd-page">

    {{-- HEADER --}}
    <div class="sd-header">
        <div class="sd-header-l">
            <div class="sd-hex">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <div class="sd-title">
                    Vente <span>#{{ $sale->id }}</span>
                </div>
                <div class="sd-sub">Détails complets de la transaction</div>
            </div>
        </div>
        <div class="sd-header-actions" style="display: flex; gap: 10px;">
            <a href="{{ route('sales.invoice', $sale->id) }}" class="btn-primary">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Facture
            </a>
            <a href="{{ route('sales.index') }}" class="btn-secondary">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="sd-stats">
        <div class="sd-stat c-a">
            <div class="sd-stat-top">
                <span class="sd-stat-label">Montant total</span>
                <div class="sd-stat-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="sd-stat-val">{{ number_format($sale->total_price, 0, ',', ' ') }}</div>
            <div class="sd-stat-unit">FCFA</div>
        </div>

        <div class="sd-stat c-b">
            <div class="sd-stat-top">
                <span class="sd-stat-label">Quantité totale</span>
                <div class="sd-stat-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <div class="sd-stat-val">{{ $totalQuantity }}</div>
            <div class="sd-stat-unit">articles vendus</div>
        </div>

        <div class="sd-stat c-c">
            <div class="sd-stat-top">
                <span class="sd-stat-label">Date de vente</span>
                <div class="sd-stat-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="sd-stat-val">{{ $sale->created_at->format('d/m/Y') }}</div>
            <div class="sd-stat-unit">{{ $sale->created_at->format('H:i') }}</div>
        </div>
    </div>

    {{-- INFORMATIONS CLIENT & CAISSIER --}}
    <div class="sd-info-grid">
        {{-- Client --}}
        <div class="sd-card">
            <div class="sd-card-hd">
                <div class="sd-card-hd-l">
                    <div class="sd-card-ico">
                        <svg viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="sd-card-title">Client</span>
                </div>
                @if($sale->client)
                    <span class="sd-card-badge">Enregistré</span>
                @else
                    <span class="sd-card-badge">Non enregistré</span>
                @endif
            </div>
            <div class="sd-card-body">
                @if($sale->client)
                    <div class="sd-person">
                        <div class="sd-avatar av-client">{{ substr($sale->client->name, 0, 1) }}</div>
                        <div class="sd-person-info">
                            <div class="sd-person-name">{{ $sale->client->name }}</div>
                            @if($sale->client->email)
                                <div class="sd-person-detail">
                                    <svg viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $sale->client->email }}</span>
                                </div>
                            @endif
                            @if($sale->client->phone)
                                <div class="sd-person-detail">
                                    <svg viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $sale->client->phone }}</span>
                                </div>
                            @endif
                            @if($sale->client->address)
                                <div class="sd-person-detail">
                                    <svg viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $sale->client->address }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="sd-empty-state">
                        <div class="sd-empty-ico">
                            <svg viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="sd-empty-text">Vente sans client enregistré</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Caissier --}}
        <div class="sd-card">
            <div class="sd-card-hd">
                <div class="sd-card-hd-l">
                    <div class="sd-card-ico">
                        <svg viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="sd-card-title">Caissier</span>
                </div>
                <span class="sd-card-badge">{{ $sale->user->role ?? 'Caissier' }}</span>
            </div>
            <div class="sd-card-body">
                <div class="sd-person">
                    <div class="sd-avatar av-cashier">{{ substr($sale->user->name, 0, 1) }}</div>
                    <div class="sd-person-info">
                        <div class="sd-person-name">{{ $sale->user->name }}</div>
                        <div class="sd-person-detail">
                            <svg viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $sale->user->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLEAU DES PRODUITS --}}
    <div class="sd-card">
        <div class="sd-card-hd">
            <div class="sd-card-hd-l">
                <div class="sd-card-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="sd-card-title">Produits vendus</span>
            </div>
            <span class="sd-card-badge">{{ $sale->items->count() }} article(s)</span>
        </div>
        <div class="sd-card-body">
            <div class="sd-table-wrap">
                <table class="sd-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr>
                                <td>
                                    <div class="sd-product">
                                        <div class="sd-product-avatar">
                                            {{ substr($item->product->name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="sd-product-info">
                                            <div class="sd-product-name">{{ $item->product->name ?? 'Produit supprimé' }}</div>
                                            @if($item->product->reference ?? false)
                                                <div class="sd-product-ref">Réf: {{ $item->product->reference }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="sd-price">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</span>
                                </td>
                                <td>
                                    <span class="sd-qty-badge">{{ $item->quantity }}</span>
                                </td>
                                <td>
                                    <span class="sd-total">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: 600; color: var(--text);">TOTAL</td>
                            <td>
                                <span class="sd-grand-total">
                                    {{ number_format($sale->total_price, 0, ',', ' ') }}
                                    <span>FCFA</span>
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- FOOTER ACTIONS --}}
    <div class="sd-footer">
        <div class="sd-footer-left">
            <svg viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Vente enregistrée le {{ $sale->created_at->format('d/m/Y à H:i') }}
        </div>
        @can('admin')
            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" onclick="return confirm('Annuler cette vente ? Le stock des produits sera restauré.')">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l6 6m0-6l-6 6M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Annuler la vente
                </button>
            </form>
        @endcan
    </div>
</div>
@endsection