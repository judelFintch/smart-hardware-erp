<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Clients</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">Nouveau</a>
    </div>
    <div class="card overflow-hidden">
        @if ($customers->isEmpty())
            <x-empty-state
                title="Aucun client"
                description="Ajoutez vos clients pour suivre les ventes."
                action="Nouveau client"
                :action-href="route('customers.create')"
            />
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-2">Nom</th>
                        <th class="p-2">Téléphone</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr class="border-b">
                            <td class="p-2">{{ $customer->name }}</td>
                            <td class="p-2">{{ $customer->phone }}</td>
                            <td class="p-2">
                                <a href="{{ route('customers.edit', $customer) }}" class="text-blue-600">Modifier</a>
                                <button wire:click="delete({{ $customer->id }})" class="text-red-600 ml-2" type="button">Supprimer</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div>
        {{ $customers->links() }}
    </div>
</div>
