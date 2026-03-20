<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Répertoire clients
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Clients, contacts et accès rapide à l'édition</h1>
                <p class="mt-2 text-sm text-slate-500">Retrouve les clients enregistrés et gère leur fiche sans passer par un écran intermédiaire.</p>
            </div>
            <a href="{{ route('customers.create') }}" class="btn btn-primary" wire:navigate>Nouveau client</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Clients affichés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($customers->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre de clients visibles sur cette page.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total clients</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($customers->total()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Répertoire complet actuellement enregistré.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Téléphones renseignés</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($customers->getCollection()->filter(fn ($customer) => filled($customer->phone))->count()) }}</div>
            <div class="mt-2 text-sm text-slate-500">Contacts exploitables directement depuis la liste.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($customers->isEmpty())
            <x-empty-state
                title="Aucun client"
                description="Ajoutez vos clients pour suivre les ventes."
                action="Nouveau client"
                :action-href="route('customers.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Téléphone</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($customers as $customer)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $customer->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">Fiche client disponible pour la vente et le suivi.</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $customer->phone ?: 'Non renseigné' }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                                        <button wire:click="delete({{ $customer->id }})" class="btn btn-secondary text-red-600" type="button">Supprimer</button>
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
        {{ $customers->links() }}
    </div>
</div>
