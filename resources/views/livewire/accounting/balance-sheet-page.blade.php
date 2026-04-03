<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-amber-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                    Comptabilite
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Bilan simplifié</h1>
                <p class="mt-2 text-sm text-slate-500">L'actif est reconstitué à partir des classes 1 à 5 et le passif intègre le résultat net calculé sur la période.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="exportExcel" class="btn btn-secondary" type="button">Exporter Excel</button>
                <button wire:click="exportPdf" class="btn btn-secondary" type="button">Exporter PDF</button>
            </div>
        </div>
    </div>

    <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Début</label>
                <input wire:model.live="start" type="date" class="input mt-2">
                <p class="mt-2 text-xs text-slate-500">Utilisé pour le résultat de période intégré aux capitaux propres.</p>
            </div>
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Fin</label>
                <input wire:model.live="end" type="date" class="input mt-2">
                <p class="mt-2 text-xs text-slate-500">Le bilan retient les soldes cumulés jusqu'à cette date.</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Actif total</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($statement['totals']['assets_total'], 2) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Passif total</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($statement['totals']['liabilities_total'], 2) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Écart</div>
            <div class="mt-3 text-3xl font-semibold {{ abs($statement['totals']['difference']) < 0.0001 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($statement['totals']['difference'], 2) }}</div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Actif</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ([
                    'Actifs stables' => 'stable_assets',
                    'Stocks et encours' => 'inventory_assets',
                    'Créances et trésorerie' => 'receivables_assets',
                ] as $label => $key)
                    <div class="px-5 py-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $label }}</div>
                            <div class="text-sm font-semibold text-slate-700">{{ number_format($statement['totals'][$key], 2) }}</div>
                        </div>
                        @forelse ($statement['sections'][$key] as $row)
                            <div class="mt-3 flex items-start justify-between gap-4 text-sm">
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $row['number'] }}</div>
                                    <div class="text-slate-500">{{ $row['name'] }}</div>
                                </div>
                                <div class="font-semibold text-slate-800">{{ number_format($row['amount'], 2) }}</div>
                            </div>
                        @empty
                            <div class="mt-3 text-sm text-slate-500">Aucun solde sur ce bloc.</div>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Passif</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ([
                    'Capitaux propres' => 'equity',
                    'Dettes à long terme' => 'long_term_liabilities',
                    'Dettes à court terme' => 'current_liabilities',
                ] as $label => $key)
                    <div class="px-5 py-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $label }}</div>
                            <div class="text-sm font-semibold text-slate-700">{{ number_format($statement['totals'][$key], 2) }}</div>
                        </div>
                        @forelse ($statement['sections'][$key] as $row)
                            <div class="mt-3 flex items-start justify-between gap-4 text-sm">
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $row['number'] }}</div>
                                    <div class="text-slate-500">{{ $row['name'] }}</div>
                                </div>
                                <div class="font-semibold text-slate-800">{{ number_format($row['amount'], 2) }}</div>
                            </div>
                        @empty
                            <div class="mt-3 text-sm text-slate-500">Aucun solde sur ce bloc.</div>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
