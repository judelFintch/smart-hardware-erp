<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-emerald-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    Comptabilite
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Compte de résultat</h1>
                <p class="mt-2 text-sm text-slate-500">Le système regroupe automatiquement les classes 6, 7 et 8 pour faire ressortir le résultat de la période.</p>
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
            </div>
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Fin</label>
                <input wire:model.live="end" type="date" class="input mt-2">
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Produits</div>
            <div class="mt-3 text-3xl font-semibold text-emerald-700">{{ number_format($statement['totals']['operating_revenue'] + $statement['totals']['other_revenue'], 2) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Charges</div>
            <div class="mt-3 text-3xl font-semibold text-red-600">{{ number_format($statement['totals']['operating_expense'] + $statement['totals']['other_expense'], 2) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Résultat net</div>
            <div class="mt-3 text-3xl font-semibold {{ $statement['totals']['net_result'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($statement['totals']['net_result'], 2) }}</div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Produits</h2>
            </div>
            <div class="divide-y divide-slate-100">
                <div class="px-5 py-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Exploitation</div>
                    @forelse ($statement['sections']['operating_revenue'] as $row)
                        <div class="mt-3 flex items-start justify-between gap-4 text-sm">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $row['number'] }}</div>
                                <div class="text-slate-500">{{ $row['name'] }}</div>
                            </div>
                            <div class="font-semibold text-emerald-700">{{ number_format($row['amount'], 2) }}</div>
                        </div>
                    @empty
                        <div class="mt-3 text-sm text-slate-500">Aucun produit d'exploitation sur la période.</div>
                    @endforelse
                </div>
                <div class="px-5 py-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Hors activités ordinaires</div>
                    @forelse ($statement['sections']['other_revenue'] as $row)
                        <div class="mt-3 flex items-start justify-between gap-4 text-sm">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $row['number'] }}</div>
                                <div class="text-slate-500">{{ $row['name'] }}</div>
                            </div>
                            <div class="font-semibold text-emerald-700">{{ number_format($row['amount'], 2) }}</div>
                        </div>
                    @empty
                        <div class="mt-3 text-sm text-slate-500">Aucun produit hors activités ordinaires sur la période.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Charges</h2>
            </div>
            <div class="divide-y divide-slate-100">
                <div class="px-5 py-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Exploitation</div>
                    @forelse ($statement['sections']['operating_expense'] as $row)
                        <div class="mt-3 flex items-start justify-between gap-4 text-sm">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $row['number'] }}</div>
                                <div class="text-slate-500">{{ $row['name'] }}</div>
                            </div>
                            <div class="font-semibold text-red-600">{{ number_format($row['amount'], 2) }}</div>
                        </div>
                    @empty
                        <div class="mt-3 text-sm text-slate-500">Aucune charge d'exploitation sur la période.</div>
                    @endforelse
                </div>
                <div class="px-5 py-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Hors activités ordinaires</div>
                    @forelse ($statement['sections']['other_expense'] as $row)
                        <div class="mt-3 flex items-start justify-between gap-4 text-sm">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $row['number'] }}</div>
                                <div class="text-slate-500">{{ $row['name'] }}</div>
                            </div>
                            <div class="font-semibold text-red-600">{{ number_format($row['amount'], 2) }}</div>
                        </div>
                    @empty
                        <div class="mt-3 text-sm text-slate-500">Aucune charge hors activités ordinaires sur la période.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
