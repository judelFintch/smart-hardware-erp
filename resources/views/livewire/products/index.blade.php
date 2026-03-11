<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Articles</h1>
            <p class="text-sm text-slate-500">Catalogue des articles et prix (unités gérées).</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <form wire:submit.prevent="importCsv" class="flex flex-wrap gap-2 items-center">
                <input type="file" wire:model="importFile" class="input">
                <button type="submit" class="btn btn-secondary">Importer CSV</button>
            </form>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Nouveau</a>
        </div>
    </div>
    @error('importFile') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
    <div class="card overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="text-left border-b">
                <th class="p-2">SKU</th>
                <th class="p-2">Nom</th>
                <th class="p-2">Unité</th>
                <th class="p-2">Coût moyen</th>
                <th class="p-2">Prix vente</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="border-b">
                    <td class="p-2">{{ $product->sku }}</td>
                    <td class="p-2">{{ $product->name }}</td>
                    <td class="p-2">{{ $product->unit?->code ?? '—' }}</td>
                    <td class="p-2">{{ number_format($product->avg_cost_local, 2) }}</td>
                    <td class="p-2">{{ number_format($product->sale_price_local, 2) }}</td>
                    <td class="p-2">
                        <a href="{{ route('products.edit', $product) }}" class="text-blue-600">Modifier</a>
                        <button wire:click="delete({{ $product->id }})" class="text-red-600 ml-2" type="button">Supprimer</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
