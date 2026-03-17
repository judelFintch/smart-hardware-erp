@php
    $isManager = in_array(auth()->user()->role, ['owner', 'manager'], true);
@endphp

<div class="space-y-8">
    <div class="flex items-center justify-between gap-2">
        <div>
            <h1 class="text-xl font-semibold">Tableau de bord</h1>
            <p class="text-sm text-slate-500">Période: {{ ucfirst($period) }}</p>
        </div>
        <div class="space-x-2">
            <button wire:click="$set('period', 'daily')" class="btn btn-secondary">Journalière</button>
            <button wire:click="$set('period', 'weekly')" class="btn btn-secondary">Hebdomadaire</button>
            <button wire:click="$set('period', 'monthly')" class="btn btn-secondary">Mensuelle</button>
            <button wire:click="$set('period', 'yearly')" class="btn btn-secondary">Année</button>
        </div>
    </div>

    <section class="grid gap-6 lg:grid-cols-4">
        <div class="kpi">
            <div class="label">Ventes</div>
            <div class="value">{{ number_format($kpis['sales'], 2) }}</div>
            <div class="text-xs text-slate-400">CDF</div>
        </div>
        <div class="kpi">
            <div class="label">Coût d'achat (COGS)</div>
            <div class="value">{{ number_format($kpis['cogs'], 2) }}</div>
            <div class="text-xs text-slate-400">CDF</div>
        </div>
        <div class="kpi">
            <div class="label">Dépenses</div>
            <div class="value">{{ number_format($kpis['expenses'], 2) }}</div>
            <div class="text-xs text-slate-400">CDF</div>
        </div>
        <div class="kpi">
            <div class="label">Stock valeur</div>
            <div class="value">{{ number_format($kpis['stock_value'], 2) }}</div>
            <div class="text-xs text-slate-400">CDF</div>
        </div>
    </section>

    @if ($isManager)
        <section class="grid gap-6 lg:grid-cols-2">
            <div class="kpi">
                <div class="label">Bénéfice</div>
                <div class="value">{{ number_format($kpis['profit'], 2) }}</div>
                <div class="text-xs text-slate-400">CDF</div>
            </div>
            <div class="kpi">
                <div class="label">Crédit restant</div>
                <div class="value">{{ number_format($kpis['credit_remaining'], 2) }}</div>
                <div class="text-xs text-slate-400">CDF</div>
            </div>
        </section>
    @endif

    <section class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Flux d'activité</div>
                    <div class="text-lg font-semibold">Achats & Ventes</div>
                </div>
                @if ($isManager)
                    <a class="btn btn-secondary" href="{{ route('reports.financial') }}" wire:navigate>Voir rapports</a>
                @endif
            </div>
            <div class="card-body">
                <div class="h-48 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 border border-dashed border-slate-200 flex items-center justify-center text-slate-400">
                    Graphique à connecter
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Alertes</div>
                    <div class="text-lg font-semibold">Stock critique</div>
                </div>
            </div>
            <div class="card-body space-y-4">
                @if ($negativeStock->isEmpty() && $lowStock->isEmpty())
                    <div class="text-sm text-slate-500">Aucun stock critique détecté.</div>
                @endif
                @foreach ($negativeStock as $balance)
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $balance->product?->name }}</div>
                            <div class="text-xs text-slate-400">Quantité: {{ number_format($balance->quantity, 3) }}</div>
                        </div>
                        <span class="badge badge-danger">Rupture</span>
                    </div>
                @endforeach
                @foreach ($lowStock as $balance)
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $balance->product?->name }}</div>
                            <div class="text-xs text-slate-400">Seuil: {{ number_format($balance->product?->reorder_level ?? 0, 3) }}</div>
                        </div>
                        <span class="badge badge-warn">Bas</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Derniers achats</div>
                    <div class="text-lg font-semibold">Commandes récentes</div>
                </div>
                <a class="btn btn-secondary" href="{{ route('purchases.index') }}" wire:navigate>Voir tout</a>
            </div>
            <div class="card-body">
                @if ($recentPurchases->isEmpty())
                    <div class="text-sm text-slate-500">Aucun achat récent.</div>
                @else
                    <div class="space-y-3">
                        @foreach ($recentPurchases as $purchase)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $purchase->supplier?->name ?? 'Fournisseur' }}</div>
                                    <div class="text-xs text-slate-400">{{ $purchase->status }}</div>
                                </div>
                                <span class="badge badge-info">CDF {{ number_format($purchase->total_cost_local, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Dernières ventes</div>
                    <div class="text-lg font-semibold">Transactions</div>
                </div>
                <a class="btn btn-secondary" href="{{ route('sales.index') }}" wire:navigate>Voir tout</a>
            </div>
            <div class="card-body">
                @if ($recentSales->isEmpty())
                    <div class="text-sm text-slate-500">Aucune vente récente.</div>
                @else
                    <div class="space-y-3">
                        @foreach ($recentSales as $sale)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $sale->customer?->name ?? 'Client comptant' }}</div>
                                    <div class="text-xs text-slate-400">{{ $sale->status }}</div>
                                </div>
                                <span class="badge {{ $sale->status === 'paid' ? 'badge-success' : 'badge-warn' }}">CDF {{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Traçabilité</div>
                    <div class="text-lg font-semibold">Dernières activités</div>
                </div>
                @if ($isManager)
                    <a class="btn btn-secondary" href="{{ route('reports.activity') }}" wire:navigate>Voir tout</a>
                @endif
            </div>
            <div class="card-body">
                @if ($recentActivity->isEmpty())
                    <div class="text-sm text-slate-500">Aucune activité récente.</div>
                @else
                    <div class="space-y-3">
                        @foreach ($recentActivity as $log)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $log->description }}</div>
                                    <div class="text-xs text-slate-400">{{ $log->user?->name ?? 'Système' }}</div>
                                </div>
                                <span class="text-xs text-slate-400">{{ $log->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Raccourcis</div>
                    <div class="text-lg font-semibold">Actions rapides</div>
                </div>
            </div>
            <div class="card-body grid gap-3 md:grid-cols-2">
                <a class="btn btn-secondary" href="{{ route('products.create') }}" wire:navigate>Alt + P Article</a>
                <a class="btn btn-secondary" href="{{ route('sales.create') }}" wire:navigate>Alt + V Vente</a>
                <a class="btn btn-secondary" href="{{ route('purchases.create') }}" wire:navigate>Alt + A Achat</a>
                <a class="btn btn-secondary" href="{{ route('inventory-counts.create') }}" wire:navigate>Alt + I Inventaire</a>
                <a class="btn btn-secondary" href="{{ route('stock-transfers.create') }}" wire:navigate>Alt + T Transfert</a>
            </div>
        </div>
    </section>
</div>
