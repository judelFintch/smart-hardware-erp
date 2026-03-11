<div class="space-y-8">
    <section class="grid gap-6 lg:grid-cols-4">
        <div class="kpi">
            <div class="label">Ventes aujourd'hui</div>
            <div class="value">0,00</div>
            <div class="text-xs text-slate-400">CDF</div>
        </div>
        <div class="kpi">
            <div class="label">Bénéfice net</div>
            <div class="value">0,00</div>
            <div class="text-xs text-slate-400">CDF</div>
        </div>
        <div class="kpi">
            <div class="label">Stock total</div>
            <div class="value">0</div>
            <div class="text-xs text-slate-400">Articles</div>
        </div>
        <div class="kpi">
            <div class="label">Crédit restant</div>
            <div class="value">0,00</div>
            <div class="text-xs text-slate-400">CDF</div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Flux d'activité</div>
                    <div class="text-lg font-semibold">Achats & Ventes</div>
                </div>
                <button class="btn btn-secondary" type="button">Voir rapports</button>
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
            <div class="card-body space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium">Marteau</div>
                        <div class="text-xs text-slate-400">Seuil: 5</div>
                    </div>
                    <span class="badge badge-danger">Rupture</span>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium">Clous 1kg</div>
                        <div class="text-xs text-slate-400">Seuil: 10</div>
                    </div>
                    <span class="badge badge-warn">Bas</span>
                </div>
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
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">Fournisseur Local A</div>
                            <div class="text-xs text-slate-400">En cours</div>
                        </div>
                        <span class="badge badge-info">CDF 0,00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">Fournisseur Étranger B</div>
                            <div class="text-xs text-slate-400">En transit</div>
                        </div>
                        <span class="badge badge-info">CDF 0,00</span>
                    </div>
                </div>
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
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">Client Comptant</div>
                            <div class="text-xs text-slate-400">Payé</div>
                        </div>
                        <span class="badge badge-success">CDF 0,00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">Client Crédit</div>
                            <div class="text-xs text-slate-400">Ouvert</div>
                        </div>
                        <span class="badge badge-warn">CDF 0,00</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
