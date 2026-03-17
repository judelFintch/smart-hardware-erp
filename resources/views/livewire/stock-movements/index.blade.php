<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Historique des mouvements</h1>
            <p class="text-sm text-slate-500">Filtrez les mouvements par article, magasin ou période.</p>
        </div>
    </div>

    <div class="card p-4">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
            <div>
                <label class="block text-xs text-slate-500">Article</label>
                <select wire:model.live="product_id" class="input">
                    <option value="">Tous</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500">Magasin</label>
                <select wire:model.live="location_id" class="input">
                    <option value="">Tous</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500">Type</label>
                <select wire:model.live="type" class="input">
                    <option value="">Tous</option>
                    <option value="purchase_in">Achat</option>
                    <option value="sale_out">Vente</option>
                    <option value="transfer_in">Transfert</option>
                    <option value="adjustment_in">Ajustement +</option>
                    <option value="adjustment_out">Ajustement -</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500">Du</label>
                <input type="date" wire:model.live="date_from" class="input">
            </div>
            <div>
                <label class="block text-xs text-slate-500">Au</label>
                <input type="date" wire:model.live="date_to" class="input">
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        @if ($movements->isEmpty())
            <x-empty-state
                title="Aucun mouvement"
                description="Aucun mouvement ne correspond aux filtres."
            />
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-3">Date</th>
                        <th class="p-3">Article</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">De</th>
                        <th class="p-3">Vers</th>
                        <th class="p-3 text-right">Quantité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movements as $movement)
                        <tr class="border-b">
                            <td class="p-3 text-sm text-slate-500">{{ $movement->occurred_at?->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ $movement->product?->name }}</td>
                            <td class="p-3"><span class="badge badge-info">{{ $movement->type }}</span></td>
                            <td class="p-3">{{ $movement->fromLocation?->name ?? '—' }}</td>
                            <td class="p-3">{{ $movement->toLocation?->name ?? '—' }}</td>
                            <td class="p-3 text-right">{{ number_format($movement->quantity, 3) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div>
        {{ $movements->links() }}
    </div>
</div>
