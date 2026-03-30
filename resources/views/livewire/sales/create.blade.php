<div class="space-y-3">
    <div class="rounded-[20px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-emerald-50 px-4 py-3 shadow-sm">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Ventes</div>
                <div class="mt-1 text-xl font-semibold text-slate-900">Nouvelle vente</div>
                <p class="text-xs text-slate-500">Prix auto, stock disponible et total ligne visibles sans prendre trop d'espace.</p>
            </div>
            <a class="btn btn-secondary" href="{{ route('sales.index') }}" wire:navigate>Retour</a>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-3" data-autosave data-autosave-key="sale-create">
        <div class="rounded-[20px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="block text-xs font-medium text-slate-600">Type</label>
                <select wire:model.defer="type" class="input" required>
                    <option value="cash">Comptant</option>
                    <option value="credit">Crédit</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600">Client (crédit)</label>
                <select wire:model.defer="customer_id" class="input">
                    <option value="">-- Aucun --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600">Magasin de vente</label>
                <select wire:model.live="location_id" class="input" required @disabled(!$canSelectAnyLocation)>
                    <option value="">-- Choisir --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                    @endforeach
                </select>
                @error('location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600">Date</label>
                <input wire:model.defer="sold_at" type="datetime-local" class="input">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600">Remise globale</label>
                <input wire:model.live.debounce.300ms="global_discount_amount" type="number" step="0.01" min="0" class="input" placeholder="0.00">
            </div>
        </div>
        </div>

        <div class="rounded-[20px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-3 py-2.5">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Articles</div>
                    <div class="text-sm font-semibold text-slate-900">Lignes de vente</div>
                </div>
                <button type="button" wire:click="addItem" class="btn btn-secondary">Ajouter ligne</button>
            </div>
            <div class="space-y-1.5 p-2">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @foreach ($items as $index => $item)
                    @php
                        $productId = isset($item['product_id']) && $item['product_id'] !== '' ? (int) $item['product_id'] : null;
                        $unitPrice = $this->getItemUnitPrice($productId);
                        $availableStock = $this->getItemAvailableStock($productId);
                        $lineTotal = $this->getItemLineTotal($item);
                    @endphp
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-2.5 py-2">
                        <div class="grid grid-cols-1 gap-1.5 md:grid-cols-[1.75fr_0.62fr_0.72fr_auto] md:items-center">
                            <select wire:model.live="items.{{ $index }}.product_id" class="input">
                                <option value="">-- Article --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <input wire:model.live.debounce.300ms="items.{{ $index }}.quantity" type="number" step="0.001" class="input" placeholder="Qté">
                            <div class="rounded-lg bg-white px-2.5 py-1.5 text-right ring-1 ring-slate-200">
                                <div class="text-[11px] uppercase tracking-[0.14em] text-slate-400">PU auto</div>
                                <div class="font-semibold text-slate-900">{{ number_format($unitPrice, 2) }}</div>
                            </div>
                            <button
                                type="button"
                                wire:click="removeItem({{ $index }})"
                                class="btn btn-secondary px-3 text-red-600"
                                aria-label="Supprimer la ligne"
                                title="Supprimer la ligne"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14H6L5 6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-1.5 flex flex-wrap items-center justify-between gap-x-3 gap-y-0.5 text-xs">
                            <div class="flex flex-wrap items-center gap-3 text-slate-500">
                                <span>Stock: <span class="font-semibold text-slate-800">{{ number_format($availableStock, 3) }}</span></span>
                            </div>
                            <div class="text-slate-500">
                                Total ligne: <span class="font-semibold text-slate-900">{{ number_format($lineTotal, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-slate-100 bg-slate-50/70 px-3 py-2">
                <div class="flex flex-wrap items-center justify-end gap-x-5 gap-y-1 text-xs text-slate-600">
                    <span>Sous-total: <span class="font-semibold text-slate-900">{{ number_format($this->getSubtotalPreview(), 2) }}</span></span>
                    <span>Remise globale: <span class="font-semibold text-slate-900">{{ number_format((float) $global_discount_amount, 2) }}</span></span>
                    <span>Total net: <span class="font-semibold text-slate-900">{{ number_format($this->getTotalPreview(), 2) }}</span></span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('sales.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
