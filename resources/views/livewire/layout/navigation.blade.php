<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $isManager = in_array(auth()->user()->role, ['owner', 'manager'], true);
@endphp

<div>
    <aside class="hidden lg:flex lg:fixed lg:inset-y-0 lg:w-64 bg-white border-r border-slate-200">
        <div class="flex flex-col w-full">
            <div class="h-16 px-6 flex items-center border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-slate-900 text-white grid place-items-center font-semibold">Q</div>
                    <div>
                        <div class="text-sm uppercase tracking-[0.2em] text-slate-400">Quincaillerie</div>
                        <div class="text-lg font-semibold">Stock Manager</div>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" wire:navigate>
                    Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" wire:navigate>
                    Articles
                </a>
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}" wire:navigate>
                    Fournisseurs
                </a>
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}" wire:navigate>
                    Clients
                </a>
                <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}" wire:navigate>
                    Achats
                </a>
                <a class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}" href="{{ route('stock-transfers.create') }}" wire:navigate>
                    Transferts
                </a>
                <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}" wire:navigate>
                    Ventes
                </a>
                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}" wire:navigate>
                    Dépenses
                </a>
                <a class="nav-link {{ request()->routeIs('inventory-counts.*') ? 'active' : '' }}" href="{{ route('inventory-counts.create') }}" wire:navigate>
                    Inventaire
                </a>
                @if ($isManager)
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.financial') }}" wire:navigate>
                        Rapports
                    </a>
                @endif
            </nav>

            <div class="px-4 pb-6">
                <div class="card p-4">
                    <div class="text-xs text-slate-500">Connecté</div>
                    <div class="font-semibold">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-400">{{ auth()->user()->email }}</div>
                    <div class="mt-3 flex items-center gap-2">
                        <a class="btn btn-secondary" href="{{ route('profile') }}" wire:navigate>Profil</a>
                        <button class="btn btn-primary" wire:click="logout" type="button">Déconnexion</button>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <header class="lg:hidden sticky top-0 z-30 bg-white border-b border-slate-200">
        <div class="px-4 h-14 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-lg bg-slate-900 text-white grid place-items-center font-semibold">Q</div>
                <div class="text-sm font-semibold">Stock Manager</div>
            </div>
            <button x-data @click="$dispatch('toggle-mobile-nav')" class="btn btn-secondary">Menu</button>
        </div>
    </header>

    <div x-data="{ open: false }" @toggle-mobile-nav.window="open = !open" class="lg:hidden">
        <div x-show="open" x-transition class="fixed inset-0 z-40 bg-black/40"></div>
        <div x-show="open" x-transition class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200">
            <div class="px-4 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Navigation</div>
                <button class="btn btn-secondary" @click="open = false">Fermer</button>
            </div>
            <nav class="px-4 py-4 space-y-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" wire:navigate>Articles</a>
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}" wire:navigate>Fournisseurs</a>
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}" wire:navigate>Clients</a>
                <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}" wire:navigate>Achats</a>
                <a class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}" href="{{ route('stock-transfers.create') }}" wire:navigate>Transferts</a>
                <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}" wire:navigate>Ventes</a>
                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}" wire:navigate>Dépenses</a>
                <a class="nav-link {{ request()->routeIs('inventory-counts.*') ? 'active' : '' }}" href="{{ route('inventory-counts.create') }}" wire:navigate>Inventaire</a>
                @if ($isManager)
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.financial') }}" wire:navigate>Rapports</a>
                @endif
                <a class="nav-link" href="{{ route('profile') }}" wire:navigate>Profil</a>
            </nav>
        </div>
    </div>
</div>
