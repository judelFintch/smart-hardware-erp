<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Fournisseurs</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Nouveau</a>
    </div>
    <div class="card overflow-hidden">
        @if ($suppliers->isEmpty())
            <x-empty-state
                title="Aucun fournisseur"
                description="Ajoutez vos fournisseurs pour les achats."
                action="Nouveau fournisseur"
                :action-href="route('suppliers.create')"
            />
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-2">Nom</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Téléphone</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr class="border-b">
                            <td class="p-2">{{ $supplier->name }}</td>
                            <td class="p-2">{{ $supplier->type }}</td>
                            <td class="p-2">{{ $supplier->phone }}</td>
                            <td class="p-2">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-blue-600">Modifier</a>
                                <button wire:click="delete({{ $supplier->id }})" class="text-red-600 ml-2" type="button">Supprimer</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div>
        {{ $suppliers->links() }}
    </div>
</div>
