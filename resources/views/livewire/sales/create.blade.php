<div class="form-section">
    <div class="section-header">
        <div>
            <div class="text-sm text-slate-500">Ventes</div>
            <div class="text-lg font-semibold">Nouvelle Vente</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('sales.index') }}" wire:navigate>Retour</a>
    </div>
    <form wire:submit.prevent="save" class="section-body space-y-6" data-autosave data-autosave-key="sale-create">
        <div class="form-grid">
            <div>
                <label class="block text-sm font-medium">Type</label>
                <select wire:model.defer="type" class="input" required>
                    <option value="cash">Comptant</option>
                    <option value="credit">Crédit</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Client (crédit)</label>
                <select wire:model.defer="customer_id" class="input">
                    <option value="">-- Aucun --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Magasin de vente</label>
                <select wire:model.live="location_id" class="input" required>
                    <option value="">-- Choisir --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                    @endforeach
                </select>
                @error('location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Date</label>
                <input wire:model.defer="sold_at" type="datetime-local" class="input">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="text-sm text-slate-500">Articles</div>
                    <div class="text-lg font-semibold">Lignes de vente</div>
                </div>
                <button type="button" wire:click="addItem" class="btn btn-secondary">Ajouter ligne</button>
            </div>
            <div class="card-body space-y-3">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @foreach ($items as $index => $item)
                    @php
                        $productId = isset($item['product_id']) && $item['product_id'] !== '' ? (int) $item['product_id'] : null;
                        $unitPrice = $this->getItemUnitPrice($productId);
                        $availableStock = $this->getItemAvailableStock($productId);
                        $lineTotal = $this->getItemLineTotal($item);
                    @endphp
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-3">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-[1.4fr_0.8fr_0.8fr_auto]">
                            <select wire:model.live="items.{{ $index }}.product_id" class="input">
                                <option value="">-- Article --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <input wire:model.live.debounce.300ms="items.{{ $index }}.quantity" type="number" step="0.001" class="input" placeholder="Quantité">
                            <input wire:model.live.debounce.300ms="items.{{ $index }}.discount_amount" type="number" step="0.01" class="input" placeholder="Réduction">
                            <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-secondary">Supprimer</button>
                        </div>
                        <div class="mt-3 grid gap-2 text-sm text-slate-600 md:grid-cols-3">
                            <div class="rounded-xl bg-white px-3 py-2 ring-1 ring-slate-200">
                                <div class="text-xs uppercase tracking-[0.14em] text-slate-400">Prix unitaire auto</div>
                                <div class="mt-1 font-semibold text-slate-900">{{ number_format($unitPrice, 2) }}</div>
                            </div>
                            <div class="rounded-xl bg-white px-3 py-2 ring-1 ring-slate-200">
                                <div class="text-xs uppercase tracking-[0.14em] text-slate-400">Stock disponible</div>
                                <div class="mt-1 font-semibold text-slate-900">{{ number_format($availableStock, 3) }}</div>
                            </div>
                            <div class="rounded-xl bg-white px-3 py-2 ring-1 ring-slate-200">
                                <div class="text-xs uppercase tracking-[0.14em] text-slate-400">Total ligne</div>
                                <div class="mt-1 font-semibold text-slate-900">{{ number_format($lineTotal, 2) }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('sales.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
