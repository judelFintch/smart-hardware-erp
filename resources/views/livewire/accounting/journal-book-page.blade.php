<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-teal-50 p-6 shadow-sm">
        <div class="max-w-3xl">
            <div class="inline-flex items-center rounded-full bg-teal-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-teal-700">
                Comptabilite
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Brouillard / journal</h1>
            <p class="mt-2 text-sm text-slate-500">Parcours les écritures comptables par journal, date et pièce source.</p>
        </div>
    </div>

    <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Journal</label>
                <select wire:model.live="journal_id" class="input mt-2">
                    <option value="">Tous les journaux</option>
                    @foreach ($journals as $journal)
                        <option value="{{ $journal->id }}">{{ $journal->code }} · {{ $journal->name }}</option>
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

    <div class="space-y-4">
        @forelse ($entries as $entry)
            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $entry->journal?->code }} · {{ $entry->entry_date?->format('d/m/Y') }}</div>
                        <div class="mt-1 text-lg font-semibold text-slate-900">{{ $entry->description }}</div>
                        <div class="mt-1 text-sm text-slate-500">Référence: {{ $entry->reference ?: '—' }} · Source: {{ class_basename($entry->source_type) }} #{{ $entry->source_id }}</div>
                    </div>
                    <div class="text-sm text-slate-500">{{ $entry->user?->name ?? 'Systeme' }}</div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/80 text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Compte</th>
                                <th class="px-4 py-3">Libellé</th>
                                <th class="px-4 py-3 text-right">Débit</th>
                                <th class="px-4 py-3 text-right">Crédit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($entry->lines as $line)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $line->account?->number }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $line->account?->name }} @if($line->description) · {{ $line->description }} @endif</td>
                                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) $line->debit, 2) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) $line->credit, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-10 text-sm text-slate-500 shadow-sm">Aucune écriture sur la période sélectionnée.</div>
        @endforelse
    </div>

    <div>{{ $entries->links() }}</div>
</div>
