<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-emerald-50 p-6 shadow-sm">
        <div class="max-w-3xl">
            <div class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                Comptabilite
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Ecritures comptables generees</h1>
            <p class="mt-2 text-sm text-slate-500">Chaque operation imputee automatiquement est rattachée a sa piece source et visible ici avec ses lignes debit / credit.</p>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($entries as $entry)
            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $entry->journal?->code }} · {{ $entry->entry_date?->format('d/m/Y') }}</div>
                        <div class="mt-1 text-lg font-semibold text-slate-900">{{ $entry->description }}</div>
                        <div class="mt-1 text-sm text-slate-500">Reference: {{ $entry->reference ?: '—' }} · Source: {{ class_basename($entry->source_type) }} #{{ $entry->source_id }}</div>
                    </div>
                    <div class="text-sm text-slate-500">{{ $entry->user?->name ?? 'Systeme' }}</div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/80 text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Compte</th>
                                <th class="px-4 py-3">Libelle</th>
                                <th class="px-4 py-3 text-right">Debit</th>
                                <th class="px-4 py-3 text-right">Credit</th>
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
            <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-10 text-sm text-slate-500 shadow-sm">Aucune ecriture comptable generee.</div>
        @endforelse
    </div>

    <div>{{ $entries->links() }}</div>
</div>
