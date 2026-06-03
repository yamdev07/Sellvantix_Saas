@extends('layouts.app')

@section('title', 'Clients — Sellvantix')

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
        --pink:          #db2777;
        --indigo:        #6366f1;
        --yellow:        #eab308;
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
    .sc-page {
        max-width: 1280px;
        margin: 0 auto;
        padding: 32px 24px 64px;
    }

    /* Access denied */
    .sc-access-denied {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: var(--radius);
        padding: 32px;
        text-align: center;
        animation: fadeUp 0.35s ease both;
        margin-bottom: 24px;
    }
    .sc-access-denied svg {
        width: 48px;
        height: 48px;
        stroke: var(--danger);
        margin: 0 auto 16px;
    }
    .sc-access-denied h2 {
        font-size: 20px;
        font-weight: 700;
        color: var(--danger);
        margin-bottom: 8px;
    }
    .sc-access-denied p {
        font-size: 14px;
        color: var(--text-2);
        margin-bottom: 24px;
    }

    /* Header */
    .sc-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
        animation: fadeUp 0.35s ease both;
    }

    .sc-header-l {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .sc-hex {
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
    .sc-hex svg {
        width: 22px;
        height: 22px;
        stroke: #fff;
        fill: none;
    }

    .sc-title {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: var(--text);
    }
    .sc-title span {
        color: var(--orange);
        font-weight: 800;
    }
    .sc-sub {
        font-size: 13px;
        color: var(--text-3);
        margin-top: 4px;
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
        position: relative;
        overflow: hidden;
    }
    .btn-primary svg {
        width: 16px;
        height: 16px;
        stroke: #fff;
        fill: none;
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

    .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: transparent;
        border: 1.5px solid var(--border);
        border-radius: 40px;
        font-size: 12px;
        font-weight: 500;
        color: var(--text-2);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-outline svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
        fill: none;
    }
    .btn-outline:hover {
        border-color: var(--orange);
        color: var(--orange);
        background: var(--orange-pale);
    }

    /* Alert */
    .sc-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        border-radius: var(--radius-sm);
        margin-bottom: 24px;
        animation: fadeUp 0.35s 0.07s ease both;
        border-left: 4px solid var(--success);
        background: #f0fdf4;
        color: #166534;
    }
    .sc-alert svg {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        fill: none;
        flex-shrink: 0;
    }

    /* Stats Cards */
    .sc-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 1100px) { .sc-stats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 580px)  { .sc-stats { grid-template-columns: 1fr; } }

    .sc-stat {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
        animation: fadeUp 0.35s ease both;
    }
    .sc-stat:nth-child(2) { animation-delay:0.07s; }
    .sc-stat:nth-child(3) { animation-delay:0.14s; }
    .sc-stat:nth-child(4) { animation-delay:0.21s; }
    .sc-stat:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--orange-soft);
    }

    .sc-stat-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .sc-stat-ico {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sc-stat-ico svg {
        width: 22px;
        height: 22px;
        stroke: currentColor;
        fill: none;
    }
    .sc-stat-info {
        flex: 1;
    }
    .sc-stat-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .sc-stat-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
    }

    /* Card */
    .sc-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: border-color 0.2s;
    }
    .sc-card:hover {
        border-color: var(--orange-soft);
    }

    /* Table */
    .sc-table-wrap {
        overflow-x: auto;
    }
    .sc-table {
        width: 100%;
        border-collapse: collapse;
    }
    .sc-table thead th {
        padding: 16px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-2);
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
    }
    .sc-table thead th:last-child {
        text-align: center;
    }
    .sc-table tbody td {
        padding: 16px 20px;
        font-size: 13px;
        color: var(--text-2);
        border-bottom: 1px solid var(--border-light);
        vertical-align: middle;
    }
    .sc-table tbody td:last-child {
        text-align: center;
    }
    .sc-table tbody tr {
        transition: background 0.15s;
    }
    .sc-table tbody tr:hover td {
        background: var(--orange-pale);
    }

    /* Client */
    .sc-client {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .sc-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--orange), var(--orange-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        box-shadow: var(--shadow-sm);
    }
    .sc-client-info {
        flex: 1;
    }
    .sc-client-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 2px;
    }
    .sc-client-id {
        font-size: 11px;
        color: var(--text-3);
    }

    /* Contact */
    .sc-contact {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-2);
        margin-bottom: 4px;
    }
    .sc-contact svg {
        width: 14px;
        height: 14px;
        stroke: var(--text-3);
        fill: none;
    }
    .sc-contact-empty {
        font-size: 12px;
        color: var(--text-3);
        font-style: italic;
    }

    /* Date */
    .sc-date {
        font-size: 13px;
        font-weight: 500;
        color: var(--text);
    }
    .sc-time {
        font-size: 11px;
        color: var(--text-3);
        margin-top: 2px;
    }

    /* Actions */
    .sc-actions {
        display: flex;
        justify-content: center;
        gap: 8px;
    }
    .sc-btn {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s;
        background: var(--card);
    }
    .sc-btn svg {
        width: 15px;
        height: 15px;
        stroke: currentColor;
        fill: none;
    }
    .sc-btn:hover {
        transform: scale(1.08);
        box-shadow: var(--shadow-sm);
    }
    .sc-btn-edit {
        border-color: var(--orange-soft);
        color: var(--orange);
    }
    .sc-btn-edit:hover {
        background: var(--orange);
        border-color: var(--orange);
        color: #fff;
    }
    .sc-btn-delete {
        border-color: #fecaca;
        color: var(--danger);
    }
    .sc-btn-delete:hover {
        background: var(--danger);
        border-color: var(--danger);
        color: #fff;
    }

    /* Empty state */
    .sc-empty {
        padding: 64px 24px;
        text-align: center;
    }
    .sc-empty-ico {
        width: 72px;
        height: 72px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }
    .sc-empty-ico svg {
        width: 32px;
        height: 32px;
        stroke: var(--text-3);
        fill: none;
    }
    .sc-empty h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
    }
    .sc-empty p {
        font-size: 14px;
        color: var(--text-2);
        margin-bottom: 20px;
    }

    /* Pagination */
    .sc-pagination {
        background: #f8fafc;
        border-top: 1px solid var(--border);
        padding: 16px 24px;
    }
    .sc-pagination nav { width: 100%; }
    .sc-pagination .pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        list-style: none;
        flex-wrap: wrap;
    }
    .sc-pagination .page-item .page-link {
        padding: 7px 14px;
        border-radius: 9px;
        border: 1.5px solid var(--border);
        color: var(--text-2);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.18s;
        display: block;
        background: var(--card);
    }
    .sc-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--orange), var(--orange-dark));
        border-color: var(--orange);
        color: #fff;
        box-shadow: 0 4px 12px rgba(249,115,22,0.3);
    }
    .sc-pagination .page-item .page-link:hover {
        border-color: var(--orange);
        color: var(--orange);
        background: var(--orange-pale);
    }
</style>
@endsection

@section('content')
<div class="sc-page">

    {{-- Vérification d'accès --}}
    @if(!auth()->user()->canManageSales())
        <div class="sc-access-denied">
            <svg viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <h2>Accès refusé</h2>
            <p>Vous n'avez pas les droits pour gérer les clients.</p>
            <a href="{{ route('dashboard') }}" class="btn-outline">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour au tableau de bord
            </a>
        </div>
    @else

    {{-- HEADER --}}
    <div class="sc-header">
        <div class="sc-header-l">
            <div class="sc-hex">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <div class="sc-title">
                    Gestion des <span>clients</span>
                </div>
                <div class="sc-sub">Consultez et gérez votre base de clients</div>
            </div>
        </div>
        @if(auth()->user()->canManageSales())
            <a href="{{ route('clients.create') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Nouveau client
            </a>
        @endif
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="sc-alert">
            <svg viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="sc-alert" style="background: #fef2f2; border-left-color: var(--danger); color: #991b1b;">
            <svg viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- STATS CARDS --}}
    <div class="sc-stats">
        <div class="sc-stat">
            <div class="sc-stat-content">
                <div class="sc-stat-ico" style="background: #eff6ff; color: var(--info);">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="sc-stat-info">
                    <div class="sc-stat-label">Total Clients</div>
                    <div class="sc-stat-value">{{ $clients->total() }}</div>
                </div>
            </div>
        </div>

        <div class="sc-stat">
            <div class="sc-stat-content">
                <div class="sc-stat-ico" style="background: #f0fdf4; color: var(--success);">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="sc-stat-info">
                    <div class="sc-stat-label">Aujourd'hui</div>
                    <div class="sc-stat-value">{{ $clients->where('created_at', '>=', today())->count() }}</div>
                </div>
            </div>
        </div>

        <div class="sc-stat">
            <div class="sc-stat-content">
                <div class="sc-stat-ico" style="background: #f5f3ff; color: var(--purple);">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="sc-stat-info">
                    <div class="sc-stat-label">Avec email</div>
                    <div class="sc-stat-value">{{ $clients->where('email', '!=', null)->count() }}</div>
                </div>
            </div>
        </div>

        <div class="sc-stat">
            <div class="sc-stat-content">
                <div class="sc-stat-ico" style="background: var(--orange-pale); color: var(--orange);">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <div class="sc-stat-info">
                    <div class="sc-stat-label">Avec téléphone</div>
                    <div class="sc-stat-value">{{ $clients->where('phone', '!=', null)->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="sc-card">
        <div class="sc-table-wrap">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Coordonnées</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        @php
                            $canEdit = auth()->user()->isSuperAdminGlobal() || 
                                      auth()->user()->isSuperAdmin() || 
                                      auth()->user()->isAdmin();
                        @endphp
                        <tr>
                            <td>
                                <div class="sc-client">
                                    <div class="sc-avatar">
                                        {{ strtoupper(substr($client->name, 0, 1)) }}
                                    </div>
                                    <div class="sc-client-info">
                                        <div class="sc-client-name">{{ $client->name }}</div>
                                        <div class="sc-client-id">ID: {{ $client->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if($client->email)
                                        <div class="sc-contact">
                                            <svg viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            {{ $client->email }}
                                        </div>
                                    @endif
                                    @if($client->phone)
                                        <div class="sc-contact">
                                            <svg viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            {{ $client->phone }}
                                        </div>
                                    @endif
                                    @if(!$client->email && !$client->phone)
                                        <div class="sc-contact-empty">Aucune coordonnée</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="sc-date">{{ $client->created_at->format('d/m/Y') }}</div>
                                <div class="sc-time">{{ $client->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                @if($canEdit)
                                    <div class="sc-actions">
                                        <a href="{{ route('clients.edit', $client->id) }}" class="sc-btn sc-btn-edit" title="Éditer">
                                            <svg viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" 
                                              onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce client ?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sc-btn sc-btn-delete" title="Supprimer">
                                                <svg viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="sc-contact-empty">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="sc-empty">
                                    <div class="sc-empty-ico">
                                        <svg viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <h3>Aucun client trouvé</h3>
                                    <p>Commencez par ajouter votre premier client</p>
                                    @if(auth()->user()->canManageSales())
                                        <a href="{{ route('clients.create') }}" class="btn-primary">
                                            <svg viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Ajouter un client
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($clients->hasPages())
            <div class="sc-pagination">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

    @endif {{-- Fin de la condition d'autorisation --}}
</div>
@endsection