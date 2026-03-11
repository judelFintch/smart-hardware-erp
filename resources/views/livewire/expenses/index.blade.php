<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Dépenses</h1>
        <a href="{{ route('expenses.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Nouvelle</a>
    </div>
    <table class="w-full bg-white shadow rounded">
        <thead>
            <tr class="text-left border-b">
                <th class="p-2">Date</th>
                <th class="p-2">Catégorie</th>
                <th class="p-2">Description</th>
                <th class="p-2">Montant</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
                <tr class="border-b">
                    <td class="p-2">{{ $expense->spent_at }}</td>
                    <td class="p-2">{{ $expense->category }}</td>
                    <td class="p-2">{{ $expense->description }}</td>
                    <td class="p-2">{{ number_format($expense->amount, 2) }}</td>
                    <td class="p-2">
                        <a href="{{ route('expenses.edit', $expense) }}" class="text-blue-600">Modifier</a>
                        <button wire:click="delete({{ $expense->id }})" class="text-red-600 ml-2" type="button">Supprimer</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
