<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-indigo-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-700">
                    Comptabilite
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Grand livre</h1>
                <p class="mt-2 text-sm text-slate-500">Consulte les mouvements détaillés d’un compte avec son solde progressif.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="exportExcel" class="btn btn-secondary" type="button">Exporter Excel</button>
                <button wire:click="exportPdf" class="btn btn-secondary" type="button">Exporter PDF</button>
            </div>
        </div>
    </div>

    <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Compte</label>
                <select wire:model.live="account_id" class="input mt-2">
                    <option value="">Tous les comptes</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->number }} · {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
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

    <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-sm text-slate-500">
            @if ($selectedAccount)
                Compte sélectionné: <span class="font-medium text-slate-900">{{ $selectedAccount->number }} · {{ $selectedAccount->name }}</span>
            @else
                Vue multi-comptes.
            @endif
            Solde initial: <span class="font-medium text-slate-900">{{ number_format($openingBalance, 2) }}</span>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50/80">
                    <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Compte</th>
                        <th class="px-4 py-3">Journal</th>
                        <th class="px-4 py-3">Référence</th>
                        <th class="px-4 py-3">Libellé</th>
                        <th class="px-4 py-3 text-right">Débit</th>
                        <th class="px-4 py-3 text-right">Crédit</th>
                        <th class="px-4 py-3 text-right">Solde</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($lines as $line)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-4 text-slate-700">{{ $line->entry?->entry_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $line->account?->number }}</div>
                                <div class="mt-1 text-sm text-slate-600">{{ $line->account?->name }}</div>
                            </td>
                            <td class="px-4 py-4 text-slate-700">{{ $line->entry?->journal?->code }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $line->entry?->reference ?: '—' }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $line->description ?: $line->entry?->description }}</td>
                            <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $line->debit, 2) }}</td>
                            <td class="px-4 py-4 text-right text-slate-700">{{ number_format((float) $line->credit, 2) }}</td>
                            <td class="px-4 py-4 text-right font-semibold {{ (float) $line->running_balance >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format((float) $line->running_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-sm text-slate-500">Aucun mouvement trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $lines->links() }}</div>
</div>
