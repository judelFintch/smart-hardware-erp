<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-blue-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">
                    Comptabilite
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Balance comptable</h1>
                <p class="mt-2 text-sm text-slate-500">Visualise les totaux débit, crédit et le solde de chaque compte sur la période choisie.</p>
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
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Débit total</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totals['debit'], 2) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Crédit total</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totals['credit'], 2) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Solde net</div>
            <div class="mt-3 text-3xl font-semibold {{ $totals['balance'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($totals['balance'], 2) }}</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50/80">
                    <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">Compte</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3 text-right">Débit</th>
                        <th class="px-4 py-3 text-right">Crédit</th>
                        <th class="px-4 py-3 text-right">Solde</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $row->number }}</div>
                                <div class="mt-1 text-sm text-slate-600">{{ $row->name }}</div>
                            </td>
                            <td class="px-4 py-4 text-slate-700">{{ $row->type }}</td>
                            <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $row->total_debit, 2) }}</td>
                            <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $row->total_credit, 2) }}</td>
                            <td class="px-4 py-4 text-right font-semibold {{ (float) $row->balance >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format((float) $row->balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-sm text-slate-500">Aucun mouvement sur la période.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
