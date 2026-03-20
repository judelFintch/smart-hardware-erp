<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="max-w-2xl">
            <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                Reporting financier
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Performance, crédit restant et valorisation du stock</h1>
            <p class="mt-2 text-sm text-slate-500">Filtre une période pour analyser les ventes, les coûts, les dépenses et l’état du stock par entité.</p>
        </div>
    </div>

    <form wire:submit.prevent="applyFilter" class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto] md:items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Début</label>
                <input wire:model.defer="start" type="date" class="input mt-2">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Fin</label>
                <input wire:model.defer="end" type="date" class="input mt-2">
            </div>
            <button class="btn btn-primary" type="submit">Filtrer</button>
        </div>
    </form>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ventes</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($salesTotal, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Chiffre d’affaires sur la période filtrée.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Coût vendu</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($cogsTotal, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Coût d’achat des produits effectivement sortis.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Dépenses</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($expensesTotal, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Charges opérationnelles comptabilisées.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Bénéfice</div>
            <div class="mt-3 text-3xl font-semibold {{ $profit >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($profit, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Résultat estimé après coût vendu et dépenses.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Crédit restant</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($creditOutstanding, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Montant encore dû sur les ventes à crédit.</div>
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Stocks par entité</h2>
            <p class="mt-1 text-sm text-slate-500">Lecture des quantités, coûts moyens et prix de vente par lieu de stockage.</p>
        </div>

        @foreach ($stockByLocation as $entry)
            <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $entry['location']->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $entry['balances']->count() }} article(s) valorisé(s) dans cette entité.</p>
                        </div>
                        <div class="text-sm text-slate-500">
                            Qté cumulée: {{ number_format((float) $entry['balances']->sum('quantity'), 3) }}
                        </div>
                    </div>
                </div>

                @if ($entry['balances']->isEmpty())
                    <div class="px-6 py-8 text-sm text-slate-500">Aucun stock valorisé pour cette entité.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50/80">
                                <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                    <th class="px-4 py-3">Article</th>
                                    <th class="px-4 py-3 text-right">Quantité</th>
                                    <th class="px-4 py-3 text-right">Coût moyen</th>
                                    <th class="px-4 py-3 text-right">Prix vente</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($entry['balances'] as $balance)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-4 py-4 font-medium text-slate-900">{{ $balance->product->name }}</td>
                                        <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $balance->quantity, 3) }}</td>
                                        <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $balance->avg_cost_local, 2) }}</td>
                                        <td class="px-4 py-4 text-right font-semibold text-slate-900">{{ number_format((float) $balance->sale_price_local, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
