@extends('layouts.app')

@section('title', 'Nouvelle vente — Sellvantix')

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
        --danger-pale:   #fef2f2;
        --shadow-sm:     0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
        --shadow-md:     0 4px 16px rgba(15,23,42,.07);
        --shadow-orange: 0 8px 24px rgba(249,115,22,.25);
        --radius:        18px;
        --radius-sm:     10px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; }

    /* PAGE */
    .sv-page { max-width: 980px; margin: 0 auto; padding: 32px 24px 72px; }

    /* HEADER */
    .sv-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 14px; }
    .sv-header-l { display: flex; align-items: center; gap: 14px; }

    .sv-hex {
        width: 46px; height: 46px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--orange), var(--orange-dark));
        clip-path: polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
        display: flex; align-items: center; justify-content: center;
        box-shadow: var(--shadow-orange);
    }
    .sv-hex svg { width: 22px; height: 22px; stroke: #fff; fill: none; }

    .sv-page-title { font-size: 22px; font-weight: 700; letter-spacing: -.3px; line-height: 1.1; }
    .sv-page-sub   { font-size: 13px; color: var(--text-3); margin-top: 3px; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 20px; background: var(--card);
        border: 1.5px solid var(--border); border-radius: 40px;
        font-size: 13px; font-weight: 500; color: var(--text-2);
        text-decoration: none; box-shadow: var(--shadow-sm);
        transition: border-color .2s, color .2s;
    }
    .btn-back svg { width: 14px; height: 14px; stroke: currentColor; fill: none; }
    .btn-back:hover { border-color: var(--orange); color: var(--orange); }

    /* ALERT */
    .sv-alert { display: none; background: var(--danger-pale); border: 1px solid rgba(220,38,38,.18); border-left: 4px solid var(--danger); border-radius: var(--radius-sm); padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: var(--danger); }
    .sv-alert.show { display: block; }
    .sv-alert ul { margin: 8px 0 0 16px; }
    .sv-alert li  { margin-bottom: 3px; }

    /* CARD */
    .sv-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); margin-bottom: 16px; overflow: hidden; animation: fadeUp .35s ease both; }
    .sv-card:nth-child(2) { animation-delay: .07s; }
    .sv-card:nth-child(3) { animation-delay: .14s; }
    .sv-card:nth-child(4) { animation-delay: .21s; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

    .sv-card-hd { display: flex; align-items: center; justify-content: space-between; padding: 16px 22px; border-bottom: 1px solid var(--border); background: #fafbfd; }
    .sv-card-hd-l { display: flex; align-items: center; gap: 10px; }
    .sv-card-ico { width: 32px; height: 32px; border-radius: 9px; background: var(--orange-pale); display: flex; align-items: center; justify-content: center; }
    .sv-card-ico svg { width: 16px; height: 16px; stroke: var(--orange); fill: none; }
    .sv-card-title { font-size: 14px; font-weight: 600; }
    .sv-card-badge { font-size: 11px; font-weight: 500; color: var(--text-3); background: #f1f5f9; border-radius: 100px; padding: 3px 12px; }
    .sv-card-body { padding: 22px; }

    /* FORM FIELDS */
    .sv-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2); margin-bottom: 7px; }

    .sv-field-wrap { position: relative; }
    .sv-ico { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); pointer-events: none; }
    .sv-ico svg { width: 16px; height: 16px; stroke: var(--text-3); fill: none; transition: stroke .15s; }
    .sv-field-wrap:focus-within .sv-ico svg { stroke: var(--orange); }

    .sv-select, .sv-input-plain {
        width: 100%; padding: 10px 13px;
        background: #fff; border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px; color: var(--text); font-family: inherit;
        outline: none; -webkit-appearance: none; appearance: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .sv-select.padded { padding-left: 40px; }
    .sv-select:focus, .sv-input-plain:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(249,115,22,.1);
    }
    .sv-select option { background: #fff; }
    .sv-input-plain.err { border-color: rgba(220,38,38,.5); background: #fff8f8; }

    /* PRODUCT ROWS */
    .sv-product-row {
        background: #fafbfd; border: 1.5px solid var(--border);
        border-radius: 12px; padding: 16px 18px 18px;
        margin-bottom: 12px; position: relative;
        animation: fadeUp .25s ease both;
        transition: border-color .2s, box-shadow .2s;
    }
    .sv-product-row:hover { border-color: var(--orange-soft); box-shadow: 0 2px 12px rgba(249,115,22,.07); }

    .sv-row-badge {
        position: absolute; top: -10px; left: 16px;
        background: var(--orange); color: #fff;
        font-size: 10px; font-weight: 700;
        padding: 2px 12px; border-radius: 100px; letter-spacing: .4px;
    }

    .sv-row-grid {
        display: grid;
        grid-template-columns: 1fr 90px 140px 120px 42px;
        gap: 12px; align-items: end;
    }
    @media (max-width: 780px) { .sv-row-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 480px) { .sv-row-grid { grid-template-columns: 1fr; } }

    .sv-stock-hint { font-size: 11px; color: var(--text-3); margin-top: 5px; display: flex; align-items: center; gap: 4px; }
    .sv-stock-val  { font-weight: 600; color: var(--orange); }
    .sv-stock-hint.low .sv-stock-val { color: var(--danger); }

    .sv-line-total {
        padding: 10px 12px;
        background: var(--orange-pale); border: 1.5px solid var(--orange-soft);
        border-radius: var(--radius-sm);
        font-size: 13px; font-weight: 700; color: var(--orange-dark);
        text-align: center; min-height: 42px;
        display: flex; align-items: center; justify-content: center;
        transition: all .2s;
    }
    .sv-line-total.err { background: var(--danger-pale); border-color: rgba(220,38,38,.3); color: var(--danger); }

    .sv-del-btn {
        width: 42px; height: 42px; background: #fff;
        border: 1.5px solid #fecaca; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: var(--danger);
        transition: background .2s, border-color .2s; flex-shrink: 0;
    }
    .sv-del-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; }
    .sv-del-btn:hover { background: var(--danger-pale); border-color: var(--danger); }

    .sv-add-btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 11px;
        background: #fff; border: 1.5px dashed var(--orange-soft);
        border-radius: var(--radius-sm);
        font-size: 13px; font-weight: 600; color: var(--orange);
        cursor: pointer; margin-top: 4px;
        transition: background .2s, border-color .2s;
    }
    .sv-add-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; }
    .sv-add-btn:hover { background: var(--orange-pale); border-color: var(--orange); }

    /* SUMMARY */
    .sv-summary { display: grid; grid-template-columns: repeat(3, 1fr); border-radius: 12px; overflow: hidden; border: 1.5px solid var(--border); }
    .sv-sum-cell { padding: 20px; text-align: center; background: #fff; border-right: 1px solid var(--border); }
    .sv-sum-cell:last-child { border-right: none; }
    .sv-sum-label { font-size: 11px; font-weight: 600; color: var(--text-3); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 8px; }
    .sv-sum-val   { font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; }
    .sv-sum-val.orange { color: var(--orange); }

    /* ACTIONS */
    .sv-actions { display: flex; align-items: center; justify-content: flex-end; gap: 12px; flex-wrap: wrap; }

    .btn-reset {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 22px; background: var(--card);
        border: 1.5px solid var(--border); border-radius: 40px;
        font-size: 13px; font-weight: 600; color: var(--text-2);
        cursor: pointer; box-shadow: var(--shadow-sm);
        transition: border-color .2s, color .2s;
    }
    .btn-reset svg { width: 14px; height: 14px; stroke: currentColor; fill: none; }
    .btn-reset:hover { border-color: var(--text-2); color: var(--text); }

    .btn-submit {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 28px;
        background: linear-gradient(135deg, var(--orange), var(--orange-dark));
        border: none; border-radius: 40px;
        font-size: 14px; font-weight: 700; color: #fff;
        cursor: pointer; position: relative; overflow: hidden;
        box-shadow: var(--shadow-orange);
        transition: transform .2s, box-shadow .2s;
    }
    .btn-submit svg { width: 16px; height: 16px; stroke: #fff; fill: none; }
    .btn-submit::after { content:''; position:absolute; inset:0; background:linear-gradient(90deg,transparent,rgba(255,255,255,.18),transparent); transform:translateX(-100%); transition:transform .5s; }
    .btn-submit:hover::after { transform: translateX(100%); }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(249,115,22,.4); }
    .btn-submit:active { transform: none; }

    /* MODAL NOUVEAU CLIENT */
    .sv-modal-overlay {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(15,23,42,.55); padding: 20px;
        align-items: center; justify-content: center;
    }
    .sv-modal-overlay.open { display: flex; }
    .sv-modal {
        background: #fff; width: 100%; max-width: 420px;
        border-radius: 18px; overflow: hidden;
        box-shadow: 0 24px 60px rgba(15,23,42,.3);
        animation: fadeUp .2s ease both;
    }
    .sv-modal-hd {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 22px; border-bottom: 1px solid var(--border);
    }
    .sv-modal-title { font-size: 16px; font-weight: 700; color: var(--text); }
    .sv-modal-close {
        background: none; border: none; font-size: 26px; line-height: 1;
        color: var(--text-3); cursor: pointer; transition: color .15s;
    }
    .sv-modal-close:hover { color: var(--text); }
    .sv-modal-body { padding: 20px 22px; }
    .sv-modal-ft {
        display: flex; align-items: center; justify-content: flex-end; gap: 10px;
        padding: 16px 22px; border-top: 1px solid var(--border); background: #fafbfd;
    }
    .sv-modal-err {
        display: none; background: var(--danger-pale, #fef2f2);
        border: 1px solid rgba(220,38,38,.3); border-radius: 10px;
        padding: 10px 12px; margin-bottom: 14px;
        font-size: 12.5px; color: var(--danger);
    }
    .sv-modal-err.show { display: block; }
</style>
@endsection

@section('content')
<div class="sv-page">

    {{-- HEADER --}}
    <div class="sv-header">
        <div class="sv-header-l">
            <div class="sv-hex">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="sv-page-title">Nouvelle vente</div>
                <div class="sv-page-sub">Enregistrer une nouvelle transaction</div>
            </div>
        </div>
        <a href="{{ route('sales.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour aux ventes
        </a>
    </div>

    {{-- Alert --}}
    <div class="sv-alert" id="svAlert">
        <strong>Veuillez corriger les erreurs suivantes :</strong>
        <ul id="svAlertList"></ul>
    </div>

    <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
        @csrf

        {{-- CLIENT --}}
        <div class="sv-card">
            <div class="sv-card-hd">
                <div class="sv-card-hd-l">
                    <div class="sv-card-ico"><svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                    <span class="sv-card-title">Client</span>
                </div>
                <span class="sv-card-badge">Optionnel</span>
            </div>
            <div class="sv-card-body">
                <label class="sv-label" for="client_id">Sélectionner un client</label>
                <div class="sv-field-wrap">
                    <span class="sv-ico"><svg viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></span>
                    <select id="client_id" name="client_id" class="sv-select padded">
                        <option value="">— Vente sans client enregistré —</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}{{ $client->phone ? ' · '.$client->phone : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="sv-add-btn" id="openNewClientBtn" style="margin-top:10px;">
                    <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nouveau client
                </button>
            </div>
        </div>

        {{-- MODAL : création rapide de client --}}
        <div class="sv-modal-overlay" id="newClientModal">
            <div class="sv-modal">
                <div class="sv-modal-hd">
                    <span class="sv-modal-title">Nouveau client</span>
                    <button type="button" class="sv-modal-close" id="closeNewClientBtn" aria-label="Fermer">&times;</button>
                </div>
                <div class="sv-modal-body">
                    <div class="sv-modal-err" id="newClientErr"></div>
                    <label class="sv-label" for="nc_name">Nom <span style="color:var(--danger)">*</span></label>
                    <input type="text" id="nc_name" class="sv-input-plain" placeholder="Nom du client" autocomplete="off" style="margin-bottom:12px;">
                    <label class="sv-label" for="nc_phone">Téléphone</label>
                    <input type="text" id="nc_phone" class="sv-input-plain" placeholder="Ex : 0190000000" inputmode="numeric" autocomplete="off" style="margin-bottom:12px;">
                    <label class="sv-label" for="nc_email">Email</label>
                    <input type="email" id="nc_email" class="sv-input-plain" placeholder="email@exemple.com (optionnel)" autocomplete="off">
                </div>
                <div class="sv-modal-ft">
                    <button type="button" class="btn-reset" id="cancelNewClientBtn">Annuler</button>
                    <button type="button" class="btn-submit" id="saveNewClientBtn">
                        <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>

        {{-- PRODUITS --}}
        <div class="sv-card">
            <div class="sv-card-hd">
                <div class="sv-card-hd-l">
                    <div class="sv-card-ico"><svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                    <span class="sv-card-title">Produits</span>
                </div>
                <span class="sv-card-badge" id="productCountLabel">1 produit</span>
            </div>
            <div class="sv-card-body">
                <div id="productsContainer">
                    <div class="sv-product-row" data-index="0">
                        <div class="sv-row-badge">Produit 1</div>
                        <div class="sv-row-grid">
                            <div>
                                <label class="sv-label">Produit</label>
                                <select name="products[0][product_id]" class="product-select sv-input-plain">
                                    <option value="">Choisir un produit…</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" data-stock="{{ $product->stock }}" data-name="{{ $product->name }}">
                                            {{ $product->name }} ({{ $product->stock }}) · {{ number_format($product->sale_price,0,',',' ') }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="sv-label">Qté</label>
                                <input type="number" name="products[0][quantity]" min="1" value="1" class="quantity-input sv-input-plain">
                                <div class="sv-stock-hint">Stock : <span class="sv-stock-val available-stock">—</span></div>
                            </div>
                            <div>
                                <label class="sv-label">Prix unit. (FCFA)</label>
                                <input type="number" name="products[0][unit_price]" min="0" step="1" value="0" class="unit-price-input sv-input-plain">
                            </div>
                            <div>
                                <label class="sv-label">Total ligne</label>
                                <div class="total-price-display sv-line-total">0 FCFA</div>
                            </div>
                            <div style="display:flex;align-items:flex-end;">
                                <button type="button" class="remove-product sv-del-btn">
                                    <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="addProduct" class="sv-add-btn">
                    <svg viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Ajouter un produit
                </button>
            </div>
        </div>

        {{-- RÉCAPITULATIF --}}
        <div class="sv-card">
            <div class="sv-card-hd">
                <div class="sv-card-hd-l">
                    <div class="sv-card-ico"><svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
                    <span class="sv-card-title">Récapitulatif</span>
                </div>
            </div>
            <div class="sv-card-body">
                <div class="sv-summary">
                    <div class="sv-sum-cell">
                        <div class="sv-sum-label">Produits</div>
                        <div class="sv-sum-val" id="productCount">1</div>
                    </div>
                    <div class="sv-sum-cell">
                        <div class="sv-sum-label">Quantité totale</div>
                        <div class="sv-sum-val" id="totalQuantity">0</div>
                    </div>
                    <div class="sv-sum-cell">
                        <div class="sv-sum-label">Montant total</div>
                        <div class="sv-sum-val orange" id="grandTotal">0 FCFA</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="sv-actions">
            <button type="button" class="btn-reset" id="resetBtn">
                <svg viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Réinitialiser
            </button>
            <button type="submit" class="btn-submit">
                <svg viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Enregistrer la vente
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowCount = 1;
    const container  = document.getElementById('productsContainer');
    const addBtn     = document.getElementById('addProduct');
    const countEl    = document.getElementById('productCount');
    const countLabel = document.getElementById('productCountLabel');
    const qtyEl      = document.getElementById('totalQuantity');
    const totalEl    = document.getElementById('grandTotal');
    const alertBox   = document.getElementById('svAlert');
    const alertList  = document.getElementById('svAlertList');
    const optHTML    = container.querySelector('.product-select')?.innerHTML ?? '';

    function initRow(row) {
        const sel   = row.querySelector('.product-select');
        const qty   = row.querySelector('.quantity-input');
        const price = row.querySelector('.unit-price-input');
        const total = row.querySelector('.total-price-display');
        const stock = row.querySelector('.available-stock');
        const hint  = row.querySelector('.sv-stock-hint');
        const del   = row.querySelector('.remove-product');

        function calc() {
            const opt = sel.selectedOptions[0];
            if (!opt || !opt.value) { total.textContent = '0 FCFA'; total.classList.remove('err'); if (stock) stock.textContent = '—'; updateSummary(); return; }
            const mx = parseInt(opt.dataset.stock) || 0;
            const q  = parseInt(qty.value) || 0;
            const p  = parseFloat(price.value) || 0;
            if (stock) { stock.textContent = mx; hint?.classList.toggle('low', q > mx); }
            if (q > mx) { total.textContent = 'Stock insuffisant'; total.classList.add('err'); qty.classList.add('err'); }
            else { total.textContent = (q*p).toLocaleString('fr-FR')+' FCFA'; total.classList.remove('err'); qty.classList.remove('err'); }
            updateSummary();
        }
        function onSel() {
            const opt = sel.selectedOptions[0];
            if (opt?.value && (!price.value || price.value=='0')) price.value = parseFloat(opt.dataset.price)||0;
            calc();
        }
        sel.addEventListener('change', onSel);
        qty.addEventListener('input', calc);
        price.addEventListener('input', calc);
        del.addEventListener('click', () => {
            if (container.querySelectorAll('.sv-product-row').length <= 1) return;
            row.style.cssText = 'opacity:0;transform:translateY(-8px);transition:all .2s';
            setTimeout(() => { row.remove(); reindex(); updateSummary(); }, 200);
        });
        calc();
    }

    function reindex() {
        const rows = container.querySelectorAll('.sv-product-row');
        rowCount = rows.length;
        rows.forEach((r, i) => {
            r.dataset.index = i;
            r.querySelector('.sv-row-badge').textContent = `Produit ${i+1}`;
            r.querySelector('.product-select').name   = `products[${i}][product_id]`;
            r.querySelector('.quantity-input').name   = `products[${i}][quantity]`;
            r.querySelector('.unit-price-input').name = `products[${i}][unit_price]`;
        });
        countEl.textContent = rowCount;
        countLabel.textContent = rowCount + (rowCount>1?' produits':' produit');
    }

    function updateSummary() {
        let tQ=0, tT=0;
        container.querySelectorAll('.sv-product-row').forEach(r => {
            const opt = r.querySelector('.product-select').selectedOptions[0];
            if (!opt?.value) return;
            const q = parseInt(r.querySelector('.quantity-input').value)||0;
            const p = parseFloat(r.querySelector('.unit-price-input').value)||0;
            const s = parseInt(opt.dataset.stock)||0;
            if (q>0 && q<=s) { tQ+=q; tT+=q*p; }
        });
        qtyEl.textContent   = tQ;
        totalEl.textContent = tT.toLocaleString('fr-FR')+' FCFA';
    }

    addBtn.addEventListener('click', () => {
        const idx = rowCount;
        const row = document.createElement('div');
        row.className = 'sv-product-row'; row.dataset.index = idx;
        row.innerHTML = `
            <div class="sv-row-badge">Produit ${idx+1}</div>
            <div class="sv-row-grid">
                <div><label class="sv-label">Produit</label><select name="products[${idx}][product_id]" class="product-select sv-input-plain">${optHTML}</select></div>
                <div><label class="sv-label">Qté</label><input type="number" name="products[${idx}][quantity]" min="1" value="1" class="quantity-input sv-input-plain"><div class="sv-stock-hint">Stock : <span class="sv-stock-val available-stock">—</span></div></div>
                <div><label class="sv-label">Prix unit. (FCFA)</label><input type="number" name="products[${idx}][unit_price]" min="0" step="1" value="0" class="unit-price-input sv-input-plain"></div>
                <div><label class="sv-label">Total ligne</label><div class="total-price-display sv-line-total">0 FCFA</div></div>
                <div style="display:flex;align-items:flex-end;"><button type="button" class="remove-product sv-del-btn"><svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div>
            </div>`;
        container.appendChild(row);
        initRow(row); rowCount++; reindex(); updateSummary();
        row.scrollIntoView({ behavior:'smooth', block:'nearest' });
    });

    document.getElementById('saleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const errors = [];
        container.querySelectorAll('.sv-product-row').forEach((row, i) => {
            const sel=row.querySelector('.product-select'), qty=row.querySelector('.quantity-input'), price=row.querySelector('.unit-price-input');
            const opt=sel.selectedOptions[0];
            if (!opt?.value) { errors.push(`Article ${i+1} : sélectionnez un produit.`); sel.classList.add('err'); return; }
            sel.classList.remove('err');
            const mx=parseInt(opt.dataset.stock)||0, q=parseInt(qty.value)||0, p=parseFloat(price.value)||0;
            if (q<1)    { errors.push(`Article ${i+1} "${opt.dataset.name}" : quantité invalide.`); qty.classList.add('err'); } else qty.classList.remove('err');
            if (p<=0)   { errors.push(`Article ${i+1} "${opt.dataset.name}" : prix requis.`); price.classList.add('err'); } else price.classList.remove('err');
            if (q>mx)   { errors.push(`Article ${i+1} "${opt.dataset.name}" : stock insuffisant (max ${mx}).`); qty.classList.add('err'); }
        });
        if (errors.length) {
            alertBox.classList.add('show');
            alertList.innerHTML = errors.map(e=>`<li>${e}</li>`).join('');
            alertBox.scrollIntoView({ behavior:'smooth', block:'nearest' });
            return;
        }
        alertBox.classList.remove('show');
        this.submit();
    });

    document.getElementById('resetBtn').addEventListener('click', () => {
        container.querySelectorAll('.sv-product-row').forEach((r,i) => { if(i>0) r.remove(); });
        const f = container.querySelector('.sv-product-row');
        if (f) {
            f.querySelector('.product-select').value='';
            f.querySelector('.quantity-input').value='1';
            f.querySelector('.unit-price-input').value='0';
            f.querySelector('.total-price-display').textContent='0 FCFA';
            f.querySelector('.available-stock').textContent='—';
            f.querySelector('.total-price-display').classList.remove('err');
        }
        rowCount=1; reindex(); updateSummary(); alertBox.classList.remove('show');
    });

    container.querySelectorAll('.sv-product-row').forEach(initRow);
    updateSummary();

    /* ─────────── MODAL NOUVEAU CLIENT ─────────── */
    const ncModal   = document.getElementById('newClientModal');
    const ncErr     = document.getElementById('newClientErr');
    const ncName    = document.getElementById('nc_name');
    const ncPhone   = document.getElementById('nc_phone');
    const ncEmail   = document.getElementById('nc_email');
    const clientSel = document.getElementById('client_id');
    const saveBtn   = document.getElementById('saveNewClientBtn');
    const csrf      = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function openNcModal() {
        ncErr.classList.remove('show'); ncErr.textContent = '';
        ncName.value = ''; ncPhone.value = ''; ncEmail.value = '';
        ncModal.classList.add('open');
        setTimeout(() => ncName.focus(), 50);
    }
    function closeNcModal() { ncModal.classList.remove('open'); }

    // Téléphone : chiffres uniquement
    ncPhone.addEventListener('input', () => { ncPhone.value = ncPhone.value.replace(/[^0-9]/g, ''); });

    document.getElementById('openNewClientBtn').addEventListener('click', openNcModal);
    document.getElementById('closeNewClientBtn').addEventListener('click', closeNcModal);
    document.getElementById('cancelNewClientBtn').addEventListener('click', closeNcModal);
    ncModal.addEventListener('click', e => { if (e.target === ncModal) closeNcModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && ncModal.classList.contains('open')) closeNcModal(); });

    saveBtn.addEventListener('click', async () => {
        ncErr.classList.remove('show'); ncErr.textContent = '';
        if (!ncName.value.trim()) {
            ncErr.textContent = 'Le nom du client est obligatoire.';
            ncErr.classList.add('show');
            ncName.focus();
            return;
        }

        saveBtn.disabled = true;
        const original = saveBtn.innerHTML;
        saveBtn.textContent = 'Enregistrement…';

        try {
            const res = await fetch('{{ route('clients.quick-store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    name:  ncName.value.trim(),
                    phone: ncPhone.value.trim(),
                    email: ncEmail.value.trim(),
                }),
            });

            if (res.status === 422) {
                const data = await res.json();
                const msgs = Object.values(data.errors || {}).flat();
                ncErr.textContent = msgs.join(' ') || 'Données invalides.';
                ncErr.classList.add('show');
                return;
            }

            if (!res.ok) throw new Error('Erreur serveur');

            const data = await res.json();
            const c = data.client;
            const opt = new Option(c.name + (c.phone ? ' · ' + c.phone : ''), c.id, true, true);
            clientSel.add(opt);
            clientSel.value = c.id;
            closeNcModal();
        } catch (err) {
            ncErr.textContent = 'Impossible de créer le client. Réessayez.';
            ncErr.classList.add('show');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = original;
        }
    });
});
</script>
@endsection