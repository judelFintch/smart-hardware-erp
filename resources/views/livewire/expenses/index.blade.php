<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Dépenses
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Charges, catégories et suivi des sorties de trésorerie</h1>
                <p class="mt-2 text-sm text-slate-500">Visualise les dépenses enregistrées et garde un accès direct à la modification ou à la suppression.</p>
            </div>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary" wire:navigate>Nouvelle dépense</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Dépenses affichées</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($expenses->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre visible sur la page actuelle.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Montant page</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format((float) $expenses->getCollection()->sum('amount'), 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">Somme des dépenses affichées actuellement.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Catégories visibles</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($expenses->getCollection()->pluck('category')->filter()->unique()->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Répartition par nature sur la page courante.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($expenses->isEmpty())
            <x-empty-state
                title="Aucune dépense"
                description="Enregistrez vos dépenses opérationnelles."
                action="Nouvelle dépense"
                :action-href="route('expenses.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Catégorie</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3 text-right">Montant</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($expenses as $expense)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $expense->spent_at?->format('d/m/Y') ?? $expense->spent_at }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-medium text-cyan-700">{{ $expense->category }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ \Illuminate\Support\Str::limit($expense->description ?: 'Sans description', 70) }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Dépense opérationnelle enregistrée dans l'application.</div>
                                </td>
                                <td class="px-4 py-4 text-right font-semibold text-slate-900">{{ number_format((float) $expense->amount, 2) }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                                        <button wire:click="delete({{ $expense->id }})" class="btn btn-secondary text-red-600" type="button" data-confirm="Confirmer la suppression de cette dépense ? Elle restera restaurable depuis la corbeille.">Supprimer</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div>
        {{ $expenses->links() }}
    </div>
</div>
