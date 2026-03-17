<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Magasins & Dépôts</h1>
            <p class="text-sm text-slate-500">Gérer les lieux de stockage.</p>
        </div>
        <a href="{{ route('stock-locations.create') }}" class="btn btn-primary" wire:navigate>Nouveau</a>
    </div>

    <div class="card overflow-hidden">
        @if ($locations->isEmpty())
            <x-empty-state
                title="Aucun magasin"
                description="Créez des lieux de stockage pour suivre les mouvements."
                action="Nouveau magasin"
                :action-href="route('stock-locations.create')"
            />
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-3">Code</th>
                        <th class="p-3">Nom</th>
                        <th class="p-3">Notes</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locations as $location)
                        <tr class="border-b">
                            <td class="p-3">{{ $location->code }}</td>
                            <td class="p-3">{{ $location->name }}</td>
                            <td class="p-3 text-slate-500">{{ $location->notes }}</td>
                            <td class="p-3">
                                <a href="{{ route('stock-locations.edit', $location) }}" class="text-blue-600" wire:navigate>Modifier</a>
                                <button wire:click="delete({{ $location->id }})" class="text-red-600 ml-2" type="button">Supprimer</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div>
        {{ $locations->links() }}
    </div>
</div>
