<div class="space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Ventes</div>
            <div class="text-xl font-semibold text-slate-900">Nouvelle vente</div>
        </div>
        <a class="btn btn-secondary" href="{{ route('sales.index') }}" wire:navigate>Retour</a>
    </div>

    <form wire:submit.prevent="save" class="space-y-3" data-autosave data-autosave-key="sale-create">
        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-5">
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Type</label>
                <select wire:model.defer="type" class="input" required>
                    <option value="cash">Comptant</option>
                    <option value="credit">Crédit</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Client</label>
                <select wire:model.defer="customer_id" class="input">
                    <option value="">-- Aucun --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Magasin</label>
                <select wire:model.live="location_id" class="input" required @disabled(!$canSelectAnyLocation)>
                    <option value="">-- Choisir --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                    @endforeach
                </select>
                @error('location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Date</label>
                <input wire:model.defer="sold_at" type="datetime-local" class="input">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Remise</label>
                <input wire:model.live.debounce.300ms="global_discount_amount" type="number" step="0.01" min="0" class="input" placeholder="0.00">
            </div>
        </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="flex flex-col gap-1 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Ajout rapide</div>
                    <div class="text-sm font-semibold text-slate-900">Nom, code ou code-barres</div>
                </div>
                <div class="text-xs text-slate-500">Un clic ajoute l’article.</div>
            </div>

            <div class="mt-2">
                <input
                    wire:model.live.debounce.250ms="product_search"
                    type="text"
                    class="input"
                    placeholder="Rechercher un article..."
                >
                @error('product_search') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>

            @if (trim($product_search) !== '')
                <div class="mt-2 space-y-1.5">
                    @forelse ($quickProducts as $product)
                        @php
                            $quickStock = (float) ($availableQuantities[$product->id] ?? 0);
                            $quickPrice = $this->getItemUnitPrice($product->id);
                        @endphp
                        <button
                            type="button"
                            wire:click="addProduct({{ $product->id }})"
                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-left hover:border-slate-300 hover:bg-white"
                        >
                            <div>
                                <div class="text-sm font-semibold text-slate-900">{{ $product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $product->sku }}</div>
                            </div>
                            <div class="text-right text-xs text-slate-600">
                                <div>Stock: <span class="font-semibold text-slate-900">{{ number_format($quickStock, 3) }}</span></div>
                                <div>PU: <span class="font-semibold text-slate-900">{{ number_format($quickPrice, 2) }}</span></div>
                            </div>
                        </button>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 px-3 py-2 text-sm text-slate-500">
                            Aucun article disponible pour cette recherche dans le magasin sélectionné.
                        </div>
                    @endforelse
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-3 py-2.5">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Articles</div>
                    <div class="text-sm font-semibold text-slate-900">Lignes de vente</div>
                </div>
                <button type="button" wire:click="addItem" class="btn btn-secondary">Ajouter ligne</button>
            </div>
            <div class="space-y-2 p-2">
                @error('items') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                @foreach ($items as $index => $item)
                    @php
                        $productId = isset($item['product_id']) && $item['product_id'] !== '' ? (int) $item['product_id'] : null;
                        $unitPrice = $this->getItemUnitPrice($productId);
                        $availableStock = (float) ($availableQuantities[$productId] ?? 0);
                        $lineTotal = $this->getItemLineTotal($item);
                    @endphp
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-2">
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-[minmax(0,1.7fr)_88px_110px_84px_auto] md:items-center">
                            <select wire:model.live="items.{{ $index }}.product_id" class="input" @disabled(blank($location_id))>
                                <option value="">-- Article --</option>
                                @foreach (($productsByIndex[$index] ?? collect()) as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <div class="rounded-lg border border-cyan-200 bg-cyan-50 px-2 py-1.5 text-center">
                                <div class="text-[10px] uppercase tracking-[0.12em] text-cyan-700">Stock</div>
                                <div class="font-semibold text-cyan-900">{{ number_format($availableStock, 3) }}</div>
                            </div>
                            <input wire:model.live.debounce.300ms="items.{{ $index }}.quantity" type="number" step="0.001" class="input" placeholder="Qté">
                            <div class="rounded-lg bg-white px-2 py-1.5 text-right ring-1 ring-slate-200">
                                <div class="text-[10px] uppercase tracking-[0.12em] text-slate-400">PU</div>
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
                        <div class="mt-1.5 flex items-center justify-end text-xs text-slate-500">
                            Total ligne: <span class="ml-1 font-semibold text-slate-900">{{ number_format($lineTotal, 2) }}</span>
                        </div>
                    </div>
                @endforeach

                @if ($location_id && $availableProducts->isEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                        Aucun article en stock dans ce magasin.
                    </div>
                @endif
            </div>
            <div class="border-t border-slate-100 bg-slate-50/70 px-3 py-2.5">
                <div class="flex flex-wrap items-center justify-end gap-x-4 gap-y-1 text-xs text-slate-600">
                    <span>Sous-total <span class="ml-1 font-semibold text-slate-900">{{ number_format($this->getSubtotalPreview(), 2) }}</span></span>
                    <span>Remise <span class="ml-1 font-semibold text-slate-900">{{ number_format((float) $global_discount_amount, 2) }}</span></span>
                    <span>Total <span class="ml-1 font-semibold text-slate-900">{{ number_format($this->getTotalPreview(), 2) }}</span></span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a class="btn btn-secondary" href="{{ route('sales.index') }}" wire:navigate>Annuler</a>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
