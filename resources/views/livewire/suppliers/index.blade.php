<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Réseau fournisseurs
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Fournisseurs, type d'approvisionnement et contacts</h1>
                <p class="mt-2 text-sm text-slate-500">Centralise les partenaires achats et accède rapidement à leur fiche pour créer ou mettre à jour les commandes.</p>
            </div>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary" wire:navigate>Nouveau fournisseur</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Fournisseurs affichés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($suppliers->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre visible sur la page actuelle.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total fournisseurs</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($suppliers->total()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Base complète des partenaires d'achat.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Téléphones renseignés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($suppliers->getCollection()->filter(fn ($supplier) => filled($supplier->phone))->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Contacts directement exploitables par l'équipe achats.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($suppliers->isEmpty())
            <x-empty-state
                title="Aucun fournisseur"
                description="Ajoutez vos fournisseurs pour les achats."
                action="Nouveau fournisseur"
                :action-href="route('suppliers.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Fournisseur</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Téléphone</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($suppliers as $supplier)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $supplier->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Partenaire utilisé dans les achats et commandes fournisseur.</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $supplier->type === 'foreign' ? 'bg-violet-100 text-violet-700' : 'bg-cyan-100 text-cyan-700' }}">
                                        {{ $supplier->type === 'foreign' ? 'Import / Étranger' : 'Local' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $supplier->phone ?: 'Non renseigné' }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                                        <button wire:click="delete({{ $supplier->id }})" class="btn btn-secondary text-red-600" type="button" data-confirm="Confirmer la suppression de ce fournisseur ? Il restera restaurable depuis la corbeille.">Supprimer</button>
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
        {{ $suppliers->links() }}
    </div>
</div>
