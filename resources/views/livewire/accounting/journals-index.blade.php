<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="max-w-3xl">
            <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                Comptabilite
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Journaux comptables</h1>
            <p class="mt-2 text-sm text-slate-500">Visualise les journaux utilises par les operations et par le moteur d imputation automatique.</p>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50/80">
                    <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Nom</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Usage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($journals as $journal)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $journal->code }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $journal->name }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $journal->type }}</td>
                            <td class="px-4 py-4 text-sm text-slate-500">
                                {{ $journal->is_system ? 'Journal cree par defaut, mais selectionnable dans le parametrage.' : 'Journal manuel disponible pour l imputation des operations.' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
