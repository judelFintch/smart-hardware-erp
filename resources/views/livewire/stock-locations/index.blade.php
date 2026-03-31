<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Entités stock
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Magasins, dépôts et lieux de mouvement</h1>
                <p class="mt-2 text-sm text-slate-500">Structure les entités de stockage utilisées dans les achats, ventes, transferts et inventaires.</p>
            </div>
            <a href="{{ route('stock-locations.create') }}" class="btn btn-primary" wire:navigate>Nouvelle entité</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Entités affichées</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($locations->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre visible sur cette page.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total entités</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($locations->total()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Base des lieux de stockage disponibles.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Codes renseignés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($locations->getCollection()->filter(fn ($location) => filled($location->code))->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Clés utiles pour les règles métier et filtres.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($locations->isEmpty())
            <x-empty-state
                title="Aucun magasin"
                description="Créez des lieux de stockage pour suivre les mouvements."
                action="Nouveau magasin"
                :action-href="route('stock-locations.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Nom</th>
                            <th class="px-4 py-3">Notes</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($locations as $location)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4 font-semibold uppercase text-slate-900">{{ $location->code }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $location->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Lieu utilisable dans les mouvements et affectations stock.</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $location->notes ?: 'Aucune note' }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('stock-locations.edit', $location) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                                        <button wire:click="delete({{ $location->id }})" class="btn btn-secondary text-red-600" type="button" data-confirm="Confirmer la suppression de ce magasin ou dépôt ? Il restera restaurable depuis la corbeille.">Supprimer</button>
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
        {{ $locations->links() }}
    </div>
</div>
