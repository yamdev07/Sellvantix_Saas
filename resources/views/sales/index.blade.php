@extends('layouts.app')

@section('title', 'Historique des ventes — Sellvantix')

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
        --radius:        18px;
        --radius-sm:     10px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        background: var(--bg); color: var(--text);
        -webkit-font-smoothing: antialiased;
    }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── PAGE ── */
    .sl-page { max-width: 1440px; margin: 0 auto; padding: 32px 28px 64px; }

    /* ── HEADER ── */
    .sl-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 28px; flex-wrap: wrap; gap: 16px;
    }
    .sl-header-l { display: flex; align-items: center; gap: 14px; }

    .sl-hex {
        width: 46px; height: 46px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--orange), var(--orange-dark));
        clip-path: polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
        display: flex; align-items: center; justify-content: center;
        box-shadow: var(--shadow-orange);
    }
    .sl-hex svg { width: 22px; height: 22px; stroke: #fff; fill: none; }

    .sl-page-title { font-size: 22px; font-weight: 700; letter-spacing: -.3px; }
    .sl-page-sub   { font-size: 13px; color: var(--text-3); margin-top: 3px; }

    .btn-primary {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 22px;
        background: linear-gradient(135deg, var(--orange), var(--orange-dark));
        border: none; border-radius: 40px;
        font-size: 14px; font-weight: 600; color: #fff;
        text-decoration: none; cursor: pointer;
        box-shadow: var(--shadow-orange);
        transition: transform .2s, box-shadow .2s;
        position: relative; overflow: hidden;
    }
    .btn-primary svg { width: 16px; height: 16px; stroke: #fff; fill: none; }
    .btn-primary::after {
        content:''; position:absolute; inset:0;
        background: linear-gradient(90deg,transparent,rgba(255,255,255,.15),transparent);
        transform: translateX(-100%); transition: transform .5s;
    }
    .btn-primary:hover::after { transform: translateX(100%); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(249,115,22,.4); }

    /* ── STAT CARDS ── */
    .sl-stats {
        display: grid; grid-template-columns: repeat(4,1fr);
        gap: 16px; margin-bottom: 24px;
    }
    @media (max-width: 1100px) { .sl-stats { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 580px)  { .sl-stats { grid-template-columns: 1fr; } }

    .sl-stat {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 20px 22px;
        box-shadow: var(--shadow-sm); position: relative; overflow: hidden;
        transition: transform .2s, box-shadow .2s, border-color .2s;
        animation: fadeUp .35s ease both;
    }
    .sl-stat:nth-child(2) { animation-delay:.07s; }
    .sl-stat:nth-child(3) { animation-delay:.14s; }
    .sl-stat:nth-child(4) { animation-delay:.21s; }
    .sl-stat:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--orange-soft); }

    .sl-stat::before {
        content:''; position:absolute;
        top:14px; bottom:14px; left:0;
        width:4px; border-radius:0 4px 4px 0;
    }
    .sl-stat.c-a::before { background: var(--info); }
    .sl-stat.c-b::before { background: var(--success); }
    .sl-stat.c-c::before { background: var(--purple); }
    .sl-stat.c-d::before { background: var(--orange); }

    .sl-stat-top {
        display: flex; justify-content: space-between;
        align-items: flex-start; margin-bottom: 14px;
    }
    .sl-stat-label {
        font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .8px; color: var(--text-3);
    }
    .sl-stat-ico {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        transition: background .2s, color .2s;
    }
    .sl-stat-ico svg { width: 20px; height: 20px; stroke: currentColor; fill: none; }
    .c-a .sl-stat-ico { background: #eff6ff; color: var(--info); }
    .c-b .sl-stat-ico { background: #f0fdf4; color: var(--success); }
    .c-c .sl-stat-ico { background: #f5f3ff; color: var(--purple); }
    .c-d .sl-stat-ico { background: var(--orange-pale); color: var(--orange); }
    .sl-stat:hover .sl-stat-ico { background: var(--orange-pale); color: var(--orange); }

    .sl-stat-val  { font-size: 30px; font-weight: 800; letter-spacing: -.5px; line-height: 1; margin-bottom: 3px; }
    .sl-stat-unit { font-size: 12px; color: var(--text-3); font-weight: 500; }

    /* ── TABLE CARD ── */
    .sl-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius); box-shadow: var(--shadow-sm);
        overflow: hidden; margin-bottom: 20px;
        animation: fadeUp .35s .1s ease both;
    }

    .sl-card-hd {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 24px; border-bottom: 1px solid var(--border);
        background: #fafbfd;
    }
    .sl-card-hd-l { display: flex; align-items: center; gap: 10px; }
    .sl-card-ico {
        width: 32px; height: 32px; border-radius: 9px;
        background: var(--orange-pale);
        display: flex; align-items: center; justify-content: center;
    }
    .sl-card-ico svg { width: 16px; height: 16px; stroke: var(--orange); fill: none; }
    .sl-card-title { font-size: 15px; font-weight: 700; }
    .sl-record-count {
        font-size: 12px; font-weight: 500; color: var(--text-3);
        background: #f1f5f9; border-radius: 100px; padding: 4px 12px;
    }

    /* ── TABLE ── */
    .sl-table-wrap { overflow-x: auto; }

    .sl-table { width: 100%; border-collapse: collapse; }

    .sl-table thead th {
        padding: 12px 20px; text-align: left;
        font-size: 11px; font-weight: 700;
        letter-spacing: .8px; text-transform: uppercase; color: var(--text-3);
        background: #f8fafc; border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .sl-table thead th.center { text-align: center; }

    .sl-table tbody td {
        padding: 14px 20px; font-size: 13px;
        color: var(--text-2); border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .sl-table tbody tr:last-child td { border-bottom: none; }
    .sl-table tbody tr { transition: background .15s; }
    .sl-table tbody tr:hover td { background: var(--orange-pale); }
    .sl-table td strong { color: var(--text); font-weight: 600; }

    /* ID bubble */
    .sl-id {
        width: 32px; height: 32px; border-radius: 50%;
        background: #f1f5f9; color: var(--text-2);
        font-size: 12px; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center;
        transition: background .15s, color .15s;
    }
    tr:hover .sl-id { background: var(--orange-pale); color: var(--orange-dark); }

    /* Avatars */
    .sl-av {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .av-p { background: linear-gradient(135deg,#60a5fa,#2563eb); }
    .av-c { background: linear-gradient(135deg,#4ade80,#16a34a); }
    .av-u { background: linear-gradient(135deg,#a78bfa,#7c3aed); }

    .sl-product-row { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
    .sl-product-row:last-child { margin-bottom: 0; }
    .sl-product-name { font-weight: 600; color: var(--text); font-size: 13px; line-height: 1.2; }
    .sl-product-sub  { font-size: 11px; color: var(--text-3); }

    .sl-person { display: flex; align-items: center; gap: 9px; }

    /* Quantity badge */
    .sl-qty {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 40px;
        background: #eff6ff; color: var(--info);
        border: 1px solid #bfdbfe;
        font-size: 12px; font-weight: 700;
    }
    .sl-qty svg { width: 12px; height: 12px; stroke: currentColor; fill: none; }

    /* Amount */
    .sl-amount { font-size: 15px; font-weight: 800; color: var(--text); line-height: 1; }
    .sl-amount.orange { color: var(--orange); }
    .sl-amount-unit { font-size: 11px; color: var(--text-3); margin-top: 2px; }

    /* Date */
    .sl-date { display: flex; align-items: center; gap: 5px; font-size: 13px; color: var(--text-2); font-weight: 500; }
    .sl-date svg { width: 13px; height: 13px; stroke: var(--text-3); fill: none; }
    .sl-time { display: flex; align-items: center; gap: 4px; font-size: 11px; color: var(--text-3); margin-top: 3px; }
    .sl-time svg { width: 11px; height: 11px; stroke: currentColor; fill: none; }

    /* Action buttons */
    .sl-acts { display: flex; align-items: center; justify-content: center; gap: 6px; }
    .sl-btn {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        border: 1.5px solid; cursor: pointer; text-decoration: none;
        transition: transform .15s, box-shadow .15s, background .15s, color .15s;
        background: var(--card);
    }
    .sl-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; }
    .sl-btn:hover { transform: scale(1.08); box-shadow: var(--shadow-md); }

    .sl-btn-view   { border-color: #bfdbfe; color: var(--info); }
    .sl-btn-view:hover { background: var(--info); color: #fff; border-color: var(--info); }
    .sl-btn-print  { border-color: #bbf7d0; color: var(--success); }
    .sl-btn-print:hover { background: var(--success); color: #fff; border-color: var(--success); }
    .sl-btn-del    { border-color: #fecaca; color: var(--danger); }
    .sl-btn-del:hover { background: var(--danger); color: #fff; border-color: var(--danger); }

    /* Empty state */
    .sl-empty { padding: 64px 24px; text-align: center; }
    .sl-empty-ico {
        width: 72px; height: 72px; background: #f1f5f9; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
    }
    .sl-empty-ico svg { width: 32px; height: 32px; stroke: var(--text-3); fill: none; }
    .sl-empty h3 { font-size: 17px; font-weight: 700; margin-bottom: 6px; }
    .sl-empty p  { font-size: 14px; color: var(--text-3); margin-bottom: 20px; }

    /* Pagination */
    .sl-paging {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 14px 22px;
        box-shadow: var(--shadow-sm); margin-bottom: 20px;
        display: flex; justify-content: center;
    }
    .sl-paging nav { width: 100%; }
    .sl-paging .pagination { display: flex; justify-content: center; gap: 6px; list-style: none; flex-wrap: wrap; }
    .sl-paging .page-item .page-link {
        padding: 7px 14px; border-radius: 9px;
        border: 1.5px solid var(--border); color: var(--text-2);
        font-size: 13px; font-weight: 500; text-decoration: none;
        transition: all .18s; display: block; background: var(--card);
    }
    .sl-paging .page-item.active .page-link {
        background: linear-gradient(135deg,var(--orange),var(--orange-dark));
        border-color: var(--orange); color: #fff;
        box-shadow: 0 4px 12px rgba(249,115,22,.3);
    }
    .sl-paging .page-item .page-link:hover {
        border-color: var(--orange); color: var(--orange); background: var(--orange-pale);
    }
    .sl-paging .page-item.disabled .page-link { opacity: .4; pointer-events: none; }

    /* ── BOTTOM CARDS ── */
    .sl-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 700px) { .sl-bottom { grid-template-columns: 1fr; } }

    .sl-bcard {
        background: var(--card); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 22px;
        box-shadow: var(--shadow-sm);
        animation: fadeUp .35s .25s ease both;
        transition: border-color .2s;
    }
    .sl-bcard:hover { border-color: var(--orange-soft); }

    .sl-bcard-head { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .sl-bcard-ico {
        width: 36px; height: 36px; border-radius: 10px;
        background: var(--orange-pale);
        display: flex; align-items: center; justify-content: center;
    }
    .sl-bcard-ico svg { width: 17px; height: 17px; stroke: var(--orange); fill: none; }
    .sl-bcard-title { font-size: 14px; font-weight: 700; }

    .sl-kpi-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .sl-kpi {
        background: #fafbfd; border: 1px solid var(--border);
        border-radius: var(--radius-sm); padding: 14px;
        transition: border-color .2s;
    }
    .sl-kpi:hover { border-color: var(--orange-soft); }
    .sl-kpi-val   { font-size: 18px; font-weight: 800; color: var(--orange); letter-spacing: -.3px; line-height: 1; margin-bottom: 4px; }
    .sl-kpi-label { font-size: 11px; color: var(--text-3); font-weight: 500; }
</style>
@endsection

@section('content')
<div class="sl-page">

    {{-- HEADER --}}
    <div class="sl-header">
        <div class="sl-header-l">
            <div class="sl-hex">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="sl-page-title">Historique des ventes</div>
                <div class="sl-page-sub">Suivez l'ensemble des transactions de votre activité</div>
            </div>
        </div>
        @if(auth()->user()->canManageSales())
        <a href="{{ route('sales.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle vente
        </a>
        @endif
    </div>

    {{-- STATS --}}
    <div class="sl-stats">
        <div class="sl-stat c-a">
            <div class="sl-stat-top">
                <span class="sl-stat-label">Total ventes</span>
                <div class="sl-stat-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <div class="sl-stat-val">{{ $sales->total() }}</div>
            <div class="sl-stat-unit">transactions</div>
        </div>

        <div class="sl-stat c-b">
            <div class="sl-stat-top">
                <span class="sl-stat-label">Chiffre d'affaires</span>
                <div class="sl-stat-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="sl-stat-val">{{ number_format($sales->sum('total_price'),0,',',' ') }}</div>
            <div class="sl-stat-unit">FCFA cumulés</div>
        </div>

        <div class="sl-stat c-c">
            <div class="sl-stat-top">
                <span class="sl-stat-label">Quantité vendue</span>
                <div class="sl-stat-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="sl-stat-val">{{ $sales->sum('quantity') }}</div>
            <div class="sl-stat-unit">unités</div>
        </div>

        <div class="sl-stat c-d">
            <div class="sl-stat-top">
                <span class="sl-stat-label">Vente moyenne</span>
                <div class="sl-stat-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="sl-stat-val">{{ number_format($sales->avg('total_price') ?? 0,0,',',' ') }}</div>
            <div class="sl-stat-unit">FCFA / transaction</div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="sl-card">
        <div class="sl-card-hd">
            <div class="sl-card-hd-l">
                <div class="sl-card-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v14M14 3v14M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                </div>
                <span class="sl-card-title">Toutes les ventes</span>
            </div>
            <span class="sl-record-count">{{ $sales->total() }} enregistrement(s)</span>
        </div>

        <div class="sl-table-wrap">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produit(s)</th>
                        <th>Client</th>
                        <th>Qté</th>
                        @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin']))
                            <th>Prix achat</th>
                        @endif
                        <th>Total</th>
                        <th>Caissier</th>
                        <th>Date</th>
                        <th class="center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            {{-- ID --}}
                            <td><span class="sl-id">{{ $sale->id }}</span></td>

                            {{-- Produits --}}
                            <td>
                                @foreach($sale->items as $item)
                                    <div class="sl-product-row">
                                        <div class="sl-av av-p">{{ substr($item->product?->name ?? 'X', 0, 1) }}</div>
                                        <div>
                                            <div class="sl-product-name">{{ $item->product?->name ?? 'Produit supprimé' }}</div>
                                            <div class="sl-product-sub">× {{ $item->quantity }} · {{ number_format($item->unit_price,0,',',' ') }} FCFA</div>
                                        </div>
                                    </div>
                                @endforeach
                            </td>

                            {{-- Client --}}
                            <td>
                                <div class="sl-person">
                                    <div class="sl-av av-c">{{ substr($sale->client?->name ?? '?', 0, 1) }}</div>
                                    <span>{{ $sale->client?->name ?? 'Particulier' }}</span>
                                </div>
                            </td>

                            {{-- Qté --}}
                            <td>
                                @php $totalQty = $sale->items->sum('quantity'); @endphp
                                <span class="sl-qty">
                                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    {{ $totalQty }}
                                </span>
                            </td>

                            {{-- Prix achat (admin seulement) --}}
                            @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin']))
                                <td>
                                    @php $purchaseTotal = $sale->items->sum(fn($i) => $i->unit_price * $i->quantity); @endphp
                                    <div class="sl-amount">{{ number_format($purchaseTotal,0,',',' ') }}</div>
                                    <div class="sl-amount-unit">FCFA</div>
                                </td>
                            @endif

                            {{-- Total --}}
                            <td>
                                <div class="sl-amount orange">{{ number_format($sale->total_price,0,',',' ') }}</div>
                                <div class="sl-amount-unit">FCFA</div>
                            </td>

                            {{-- Caissier --}}
                            <td>
                                <div class="sl-person">
                                    <div class="sl-av av-u">{{ substr($sale->user?->name ?? '?', 0, 1) }}</div>
                                    <span>{{ $sale->user?->name ?? 'Inconnu' }}</span>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td>
                                <div class="sl-date">
                                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $sale->created_at->format('d/m/Y') }}
                                </div>
                                <div class="sl-time">
                                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $sale->created_at->format('H:i') }}
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="sl-acts">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="sl-btn sl-btn-view" title="Voir détails">
                                        <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    <a href="{{ route('sales.invoice', $sale->id) }}" class="sl-btn sl-btn-print" title="Imprimer facture">
                                        <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>

                                    @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin']))
                                        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                                              onsubmit="return confirm('Annuler cette vente ? Le stock sera restauré.')" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="sl-btn sl-btn-del" title="Annuler la vente">
                                                <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l6 6m0-6l-6 6M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin']) ? 9 : 8 }}">
                                <div class="sl-empty">
                                    <div class="sl-empty-ico">
                                        <svg viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <h3>Aucune vente enregistrée</h3>
                                    <p>Commencez par enregistrer votre première transaction</p>
                                    @if(auth()->user()->canManageSales())
                                    <a href="{{ route('sales.create') }}" class="btn-primary" style="display:inline-flex;margin:0 auto;">
                                        <svg viewBox="0 0 24 24" stroke-width="2.5" style="width:15px;height:15px;stroke:#fff;fill:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Créer la première vente
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($sales->hasPages())
        <div class="sl-paging">{{ $sales->links() }}</div>
    @endif

    {{-- BOTTOM STATS --}}
    <div class="sl-bottom">
        <div class="sl-bcard">
            <div class="sl-bcard-head">
                <div class="sl-bcard-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="sl-bcard-title">Performance des ventes</span>
            </div>
            <div class="sl-kpi-grid">
                <div class="sl-kpi">
                    <div class="sl-kpi-val">{{ number_format($sales->max('total_price') ?? 0,0,',',' ') }} <span style="font-size:10px;color:var(--text-3);font-weight:500;">FCFA</span></div>
                    <div class="sl-kpi-label">Plus grosse vente</div>
                </div>
                <div class="sl-kpi">
                    <div class="sl-kpi-val">{{ number_format($sales->min('total_price') ?? 0,0,',',' ') }} <span style="font-size:10px;color:var(--text-3);font-weight:500;">FCFA</span></div>
                    <div class="sl-kpi-label">Plus petite vente</div>
                </div>
            </div>
        </div>

        <div class="sl-bcard">
            <div class="sl-bcard-head">
                <div class="sl-bcard-ico">
                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="sl-bcard-title">Répartition</span>
            </div>
            <div class="sl-kpi-grid">
                <div class="sl-kpi">
                    <div class="sl-kpi-val">{{ $sales->unique('user_id')->count() }}</div>
                    <div class="sl-kpi-label">Caissiers actifs</div>
                </div>
                <div class="sl-kpi">
                    <div class="sl-kpi-val">{{ $sales->unique('client_id')->count() }}</div>
                    <div class="sl-kpi-label">Clients uniques</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection