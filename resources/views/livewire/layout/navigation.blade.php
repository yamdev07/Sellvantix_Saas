<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('landing'));
    }
}; ?>

<nav x-data="{ open: false }" class="qapp-nav">
    <!-- Primary Navigation Menu -->
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo Sellvantix - EXTRÊME GAUCHE -->
                <div class="shrink-0 flex items-center -ml-4 sm:ml-0">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                        <div class="relative">
                            <div class="qapp-logo-hex w-10 h-10">
                                <svg viewBox="0 0 24 24" stroke-width="2" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-[#f5f3ef]">Sell<em class="text-[#f97316] not-italic">vantix</em></h1>
                            <p class="text-xs text-[#55535f]">Gestion de stock</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-6 sm:flex">
                    <!-- Dashboard - Tous les utilisateurs -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate 
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>{{ __('Dashboard') }}</span>
                        </div>
                    </x-nav-link>

                    <!-- Produits - Tous les utilisateurs -->
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" wire:navigate 
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <span>Produits</span>
                        </div>
                    </x-nav-link>

                    <!-- Fournisseurs - Accessible uniquement aux admins et super admins -->
                    @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin', 'manager']))
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" wire:navigate 
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Fournisseurs</span>
                        </div>
                    </x-nav-link>
                    @endif

                    <!-- Catégories - Accessible uniquement aux admins et super admins -->
                    @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin', 'manager']))
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" wire:navigate 
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Catégories</span>
                        </div>
                    </x-nav-link>
                    @endif

                    <!-- Ventes - Tous les utilisateurs -->
                    <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" wire:navigate 
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Ventes</span>
                        </div>
                    </x-nav-link>

                    <!-- Clients - Réservé à ceux qui gèrent les ventes -->
                    @if(auth()->user()->canManageSales())
                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" wire:navigate
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.281.015-.559.03-.835.046m-11.665 0c-.276-.016-.554-.031-.835-.046m11.665 0a23.848 23.848 0 01-11.665 0" />
                            </svg>
                            <span>Clients</span>
                        </div>
                    </x-nav-link>
                    @endif

                    <!-- Historique - Accessible uniquement aux admins et super admins -->
                    @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin', 'manager']))
                    <x-nav-link :href="route('products.global-history')" :active="request()->routeIs('products.global-history')" wire:navigate 
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Historique</span>
                        </div>
                    </x-nav-link>
                    @endif

                    <!-- Rapports - Accessible uniquement aux super_admin_global, super_admin, admin -->
                    @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin', 'manager']))
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" wire:navigate 
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Rapports</span>
                        </div>
                    </x-nav-link>
                    @endif

                    <!-- ===================================================== -->
                    <!-- LIEN MON ABONNEMENT - UNIQUEMENT POUR ADMINISTRATEURS -->
                    <!-- super_admin_global, super_admin et admin uniquement -->
                    <!-- ===================================================== -->
                    @if(auth()->user()->isSuperAdminGlobal() || auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                    <x-nav-link :href="route('subscription.show')" :active="request()->routeIs('subscription.*')" wire:navigate
                        class="qapp-nav-link">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Mon abonnement</span>
                        </div>
                    </x-nav-link>
                    @endif

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="qapp-user-btn">
                            <div class="flex items-center gap-3">
                                <div class="qapp-user-avatar">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="text-left">
                                    <div class="qapp-user-name">{{ auth()->user()->name ?? 'Utilisateur' }}</div>
                                    <div class="qapp-user-role">
                                        @switch(auth()->user()->role)
                                            @case('super_admin_global')
                                                Super Administrateur Global
                                                @break
                                            @case('super_admin')
                                                Super Administrateur
                                                @break
                                            @case('admin')
                                                Administrateur
                                                @break
                                            @case('manager')
                                                Manager
                                                @break
                                            @case('storekeeper')
                                                Magasinier
                                                @break
                                            @case('cashier')
                                                Caissier
                                                @break
                                            @default
                                                Utilisateur
                                        @endswitch
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-[#55535f] group-hover:text-[#f97316] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="py-1 bg-[#111116] border border-[rgba(249,115,22,0.13)] rounded-lg">
                            <x-dropdown-link :href="route('profile')" wire:navigate
                                class="qapp-dropdown-link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if(auth()->user()->isSuperAdminGlobal() || auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('company.settings')" wire:navigate
                                class="qapp-dropdown-link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Paramètres Entreprise
                            </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link class="qapp-dropdown-link text-[#f87171]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="qapp-hamburger-btn">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#111116] border-t border-[rgba(249,115,22,0.13)]">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <!-- Dashboard -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate 
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Produits -->
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" wire:navigate 
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                Produits
            </x-responsive-nav-link>

            <!-- Fournisseurs -->
            @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin', 'manager']))
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" wire:navigate 
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Fournisseurs
            </x-responsive-nav-link>
            @endif

            <!-- Catégories -->
            @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin', 'manager']))
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" wire:navigate 
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Catégories
            </x-responsive-nav-link>
            @endif

            <!-- Ventes -->
            <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" wire:navigate 
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Ventes
            </x-responsive-nav-link>

            <!-- Clients - Réservé à ceux qui gèrent les ventes -->
            @if(auth()->user()->canManageSales())
            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" wire:navigate
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.281.015-.559.03-.835.046m-11.665 0c-.276-.016-.554-.031-.835-.046m11.665 0a23.848 23.848 0 01-11.665 0" />
                </svg>
                Clients
            </x-responsive-nav-link>
            @endif

            <!-- Historique -->
            @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin', 'manager']))
            <x-responsive-nav-link :href="route('products.global-history')" :active="request()->routeIs('products.global-history')" wire:navigate 
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Historique
            </x-responsive-nav-link>
            @endif

            <!-- Rapports -->
            @if(in_array(auth()->user()->role, ['super_admin_global', 'super_admin', 'admin', 'manager']))
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" wire:navigate 
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Rapports
            </x-responsive-nav-link>
            @endif

            <!-- ===================================================== -->
            <!-- LIEN MON ABONNEMENT - RESPONSIVE (VERSION MOBILE) -->
            <!-- UNIQUEMENT POUR ADMINISTRATEURS -->
            <!-- ===================================================== -->
            @if(auth()->user()->isSuperAdminGlobal() || auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
            <x-responsive-nav-link :href="route('subscription.show')" :active="request()->routeIs('subscription.*')" wire:navigate
                class="qapp-responsive-nav-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Mon abonnement
            </x-responsive-nav-link>
            @endif

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-[rgba(249,115,22,0.13)]">
            <div class="px-4 py-3">
                <div class="flex items-center gap-3 mb-3">
                    <div class="qapp-user-avatar w-10 h-10 text-base">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-base text-[#f5f3ef]" x-data="{{ json_encode(['name' => auth()->user()->name ?? 'Utilisateur']) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                        <div class="font-medium text-sm text-[#55535f]">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <x-responsive-nav-link :href="route('profile')" wire:navigate
                    class="qapp-responsive-nav-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if(auth()->user()->isSuperAdminGlobal() || auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('company.settings')" wire:navigate
                    class="qapp-responsive-nav-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Paramètres Entreprise
                </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link class="qapp-responsive-nav-link text-[#f87171]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>

    <style>
    /* Styles spécifiques à la navigation Sellvantix */
    .qapp-nav {
        background: #111116;
        border-bottom: 1px solid rgba(249, 115, 22, 0.13);
        position: relative;
    }

    .qapp-nav::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 10%;
        right: 10%;
        height: 1px;
        background: linear-gradient(90deg, transparent, #f97316, transparent);
    }

    .qapp-logo-hex {
        background: linear-gradient(135deg, #f97316, #ea580c);
        clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 28px rgba(249, 115, 22, 0.45);
        transition: transform 0.2s ease;
    }

    .qapp-logo-hex:hover {
        transform: rotate(-3deg) scale(1.02);
        box-shadow: 0 0 35px rgba(249, 115, 22, 0.6);
    }

    .qapp-logo-hex svg {
        stroke: white;
        fill: none;
    }

    /* Correction pour que le logo soit à l'extrême gauche */
    .qapp-nav .shrink-0:first-child {
        margin-left: -0.5rem;
    }

    @media (min-width: 640px) {
        .qapp-nav .shrink-0:first-child {
            margin-left: 0;
        }
    }

    .qapp-nav-link {
        display: flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        margin: 0 0.25rem;
        border-radius: 0.5rem;
        color: #55535f;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .qapp-nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #f97316;
        transform: scaleY(0);
        transition: transform 0.2s ease;
    }

    .qapp-nav-link:hover {
        background: rgba(249, 115, 22, 0.08);
        color: #f5f3ef;
    }

    .qapp-nav-link:hover::before {
        transform: scaleY(1);
    }

    .qapp-nav-link.active {
        background: rgba(249, 115, 22, 0.12);
        color: #f97316;
    }

    .qapp-nav-link.active::before {
        transform: scaleY(1);
    }

    .qapp-user-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        background: rgba(249, 115, 22, 0.05);
        border: 1px solid rgba(249, 115, 22, 0.13);
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .qapp-user-btn:hover {
        background: rgba(249, 115, 22, 0.12);
        border-color: #f97316;
    }

    .qapp-user-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #f97316, #ea580c);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 0 15px rgba(249, 115, 22, 0.3);
    }

    .qapp-user-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #f5f3ef;
        white-space: nowrap;
    }

    .qapp-user-role {
        font-size: 0.6875rem;
        color: #55535f;
        white-space: nowrap;
    }

    .qapp-hamburger-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        border-radius: 0.5rem;
        color: #55535f;
        background: transparent;
        border: 1px solid rgba(249, 115, 22, 0.13);
        transition: all 0.2s ease;
    }

    .qapp-hamburger-btn:hover {
        background: rgba(249, 115, 22, 0.08);
        border-color: #f97316;
        color: #f97316;
    }

    .qapp-dropdown-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        color: #f5f3ef;
        transition: all 0.2s ease;
        background: transparent;
    }

    .qapp-dropdown-link:hover {
        background: rgba(249, 115, 22, 0.08);
        color: #f97316;
    }

    .qapp-responsive-nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        color: #55535f;
        transition: all 0.2s ease;
    }

    .qapp-responsive-nav-link:hover {
        background: rgba(249, 115, 22, 0.08);
        color: #f5f3ef;
    }

    .qapp-responsive-nav-link.active {
        background: rgba(249, 115, 22, 0.12);
        color: #f97316;
    }
    </style>
</nav>