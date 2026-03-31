<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Base unités
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Unités de vente, de stock et de référence</h1>
                <p class="mt-2 text-sm text-slate-500">Structure les unités utilisées dans les articles pour éviter les erreurs de saisie et d'interprétation.</p>
            </div>
            <a href="{{ route('units.create') }}" class="btn btn-primary" wire:navigate>Nouvelle unité</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Unités affichées</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($units->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Volume visible sur la page courante.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total unités</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($units->total()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Référentiel global des unités disponibles.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Types présents</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($units->getCollection()->pluck('type')->filter()->unique()->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Catégories représentées sur cette page.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($units->isEmpty())
            <x-empty-state
                title="Aucune unité"
                description="Créez des unités pour vos articles."
                action="Nouvelle unité"
                :action-href="route('units.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Nom</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($units as $unit)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4 font-semibold text-slate-900">{{ strtoupper($unit->code) }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $unit->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Utilisable dans les articles et mouvements de stock.</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-medium text-cyan-700">{{ $unit->type }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('units.edit', $unit) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                                        <button wire:click='openDeleteModal({{ $unit->id }}, @json($unit->name))' class="btn btn-secondary text-red-600" type="button">Supprimer</button>
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
        {{ $units->links() }}
    </div>

    @include('livewire.partials.delete-secret-modal')
</div>
