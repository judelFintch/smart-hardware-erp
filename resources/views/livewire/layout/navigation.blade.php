<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function markNotificationsAsRead(): void
    {
        \App\Models\AppNotification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $isManager = in_array(auth()->user()->role, ['owner', 'manager'], true);
    $unreadNotifications = \App\Models\AppNotification::query()
        ->where('user_id', auth()->id())
        ->whereNull('read_at')
        ->count();
@endphp

<div>
    <aside class="hidden lg:flex lg:fixed lg:inset-y-0 lg:w-64 bg-white border-r border-slate-200 overflow-y-auto h-screen">
        <div class="flex flex-col w-full min-h-screen">
            <div class="px-6 py-4 border-b border-slate-100">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500 font-semibold">Navigation</div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1">
                <div class="sidebar-title">Vue</div>
                @if ($isManager)
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" wire:navigate>
                        Dashboard
                    </a>
                @endif

                <div class="sidebar-title">Catalogue</div>
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" wire:navigate>
                    Articles
                </a>
                @if ($isManager)
                    <a class="nav-link {{ request()->routeIs('stock-locations.*') ? 'active' : '' }}" href="{{ route('stock-locations.index') }}" wire:navigate>
                        Magasins & Dépôts
                    </a>
                    <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}" href="{{ route('units.index') }}" wire:navigate>
                        Unités
                    </a>
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}" wire:navigate>
                        Utilisateurs
                    </a>
                    <a class="nav-link {{ request()->routeIs('company.*') ? 'active' : '' }}" href="{{ route('company.settings') }}" wire:navigate>
                        Entreprise
                    </a>
                @endif
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}" wire:navigate>
                    Fournisseurs
                </a>
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}" wire:navigate>
                    Clients
                </a>

                <div class="sidebar-title">Opérations</div>
                <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}" wire:navigate>
                    Achats
                </a>
                <a class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}" href="{{ route('stock-transfers.create') }}" wire:navigate>
                    Transferts
                </a>
                <a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}" href="{{ route('stock-movements.index') }}" wire:navigate>
                    Mouvements
                </a>
                <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}" wire:navigate>
                    Ventes
                </a>
                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}" wire:navigate>
                    Dépenses
                </a>
                @if ($isManager)
                    <a class="nav-link {{ request()->routeIs('inventory-counts.*') ? 'active' : '' }}" href="{{ route('inventory-counts.index') }}" wire:navigate>
                        Inventaire
                    </a>
                @endif
                @if ($isManager)
                    <div class="sidebar-title">Analyse</div>
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.financial') }}" wire:navigate>
                        Rapports
                    </a>
                    <a class="nav-link {{ request()->routeIs('reports.activity') ? 'active' : '' }}" href="{{ route('reports.activity') }}" wire:navigate>
                        Journal d'activité
                    </a>
                    <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}" wire:navigate>
                        Notifications
                    </a>
                    <div class="sidebar-title">Système</div>
                    <a class="nav-link {{ request()->routeIs('system.backups') ? 'active' : '' }}" href="{{ route('system.backups') }}" wire:navigate>
                        Sauvegarde
                    </a>
                    <a class="nav-link {{ request()->routeIs('trash.*') ? 'active' : '' }}" href="{{ route('trash.index') }}" wire:navigate>
                        Corbeille
                    </a>
                    <a class="nav-link {{ request()->routeIs('system.health') ? 'active' : '' }}" href="{{ route('system.health') }}" wire:navigate>
                        Santé système
                    </a>
                @endif
            </nav>

            <div class="px-4 pb-6"></div>
        </div>
    </aside>

    <header class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-4 md:px-6 h-14">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-slate-900 text-white grid place-items-center font-semibold">Q</div>
                <div>
                    <div class="text-xs text-slate-500 uppercase tracking-[0.2em]">Quincaillerie</div>
                    <div class="text-sm font-semibold">Stock Manager</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if ($isManager)
                    <div class="relative">
                        <a class="btn btn-secondary" href="{{ route('notifications.index') }}" wire:navigate>Notifications @if($unreadNotifications > 0)<span class="ml-1 rounded-full bg-cyan-600 px-2 py-0.5 text-xs text-white">{{ $unreadNotifications }}</span>@endif</a>
                    </div>
                @endif
                <a class="btn btn-secondary" href="{{ route('profile') }}" wire:navigate>Profil</a>
                <button class="btn btn-primary" wire:click="logout" type="button">Déconnexion</button>
                <button x-data @click="$dispatch('toggle-mobile-nav')" class="btn btn-secondary lg:hidden">Menu</button>
            </div>
        </div>
    </header>

    <div x-data="{ open: false }" @toggle-mobile-nav.window="open = !open" class="lg:hidden">
        <div x-show="open" x-transition class="fixed inset-0 z-40 bg-black/40"></div>
        <div x-show="open" x-transition class="fixed inset-y-0 left-0 z-50 w-full max-w-md bg-white border-r border-slate-200 overflow-y-auto h-screen">
        <div class="px-4 py-4 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div class="font-semibold">Navigation</div>
                <button class="btn btn-secondary" @click="open = false">Fermer</button>
            </div>
            <div class="mt-3 font-semibold text-sm">Navigation</div>
        </div>
            <nav class="px-4 py-4 space-y-1">
                <div class="sidebar-title">Vue</div>
                @if ($isManager)
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                @endif

                <div class="sidebar-title">Catalogue</div>
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" wire:navigate>Articles</a>
                @if ($isManager)
                    <a class="nav-link {{ request()->routeIs('stock-locations.*') ? 'active' : '' }}" href="{{ route('stock-locations.index') }}" wire:navigate>Magasins & Dépôts</a>
                    <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}" href="{{ route('units.index') }}" wire:navigate>Unités</a>
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}" wire:navigate>Utilisateurs</a>
                    <a class="nav-link {{ request()->routeIs('company.*') ? 'active' : '' }}" href="{{ route('company.settings') }}" wire:navigate>Entreprise</a>
                @endif
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}" wire:navigate>Fournisseurs</a>
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}" wire:navigate>Clients</a>

                <div class="sidebar-title">Opérations</div>
                <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}" wire:navigate>Achats</a>
                <a class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}" href="{{ route('stock-transfers.create') }}" wire:navigate>Transferts</a>
                <a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}" href="{{ route('stock-movements.index') }}" wire:navigate>Mouvements</a>
                <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}" wire:navigate>Ventes</a>
                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}" wire:navigate>Dépenses</a>
                @if ($isManager)
                    <a class="nav-link {{ request()->routeIs('inventory-counts.*') ? 'active' : '' }}" href="{{ route('inventory-counts.index') }}" wire:navigate>Inventaire</a>
                @endif
                @if ($isManager)
                    <div class="sidebar-title">Analyse</div>
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.financial') }}" wire:navigate>Rapports</a>
                    <a class="nav-link {{ request()->routeIs('reports.activity') ? 'active' : '' }}" href="{{ route('reports.activity') }}" wire:navigate>Journal d'activité</a>
                    <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}" wire:navigate>Notifications</a>
                    <div class="sidebar-title">Système</div>
                    <a class="nav-link {{ request()->routeIs('system.backups') ? 'active' : '' }}" href="{{ route('system.backups') }}" wire:navigate>Sauvegarde</a>
                    <a class="nav-link {{ request()->routeIs('trash.*') ? 'active' : '' }}" href="{{ route('trash.index') }}" wire:navigate>Corbeille</a>
                    <a class="nav-link {{ request()->routeIs('system.health') ? 'active' : '' }}" href="{{ route('system.health') }}" wire:navigate>Santé système</a>
                @endif
                <div class="sidebar-title">Compte</div>
                <a class="nav-link" href="{{ route('profile') }}" wire:navigate>Profil</a>
            </nav>
        </div>
    </div>
</div>
