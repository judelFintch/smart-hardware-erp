<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <label class="sr-only" for="global-search">Recherche</label>
    <input
        id="global-search"
        data-global-search
        type="search"
        class="input"
        placeholder="Rechercher produits, clients, fournisseurs..."
        wire:model.debounce.400ms="query"
        @focus="open = true"
        @keydown.escape="open = false"
    />

    @php
        $hasResults = $results['products']->isNotEmpty()
            || $results['customers']->isNotEmpty()
            || $results['suppliers']->isNotEmpty()
            || $results['sales']->isNotEmpty()
            || $results['purchases']->isNotEmpty();
    @endphp

    <div x-show="open && (@js($hasResults) || @js(strlen($query ?? '') >= 2))" x-transition class="absolute z-50 mt-2 w-full rounded-xl border border-slate-200 bg-white shadow-lg">
        <div class="p-3 space-y-3 text-sm">
            @if (!$hasResults && strlen($query ?? '') >= 2)
                <div class="text-sm text-slate-400">Aucun résultat.</div>
            @endif
            @if ($results['products']->isNotEmpty())
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Articles</div>
                    <div class="space-y-1">
                        @foreach ($results['products'] as $product)
                            <a class="search-item" href="{{ route('products.edit', $product) }}" wire:navigate>
                                <span class="font-medium">{{ $product->name }}</span>
                                <span class="text-xs text-slate-400">{{ $product->sku }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($results['customers']->isNotEmpty())
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Clients</div>
                    <div class="space-y-1">
                        @foreach ($results['customers'] as $customer)
                            <a class="search-item" href="{{ route('customers.edit', $customer) }}" wire:navigate>
                                <span class="font-medium">{{ $customer->name }}</span>
                                <span class="text-xs text-slate-400">{{ $customer->phone ?? $customer->email }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($results['suppliers']->isNotEmpty())
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Fournisseurs</div>
                    <div class="space-y-1">
                        @foreach ($results['suppliers'] as $supplier)
                            <a class="search-item" href="{{ route('suppliers.edit', $supplier) }}" wire:navigate>
                                <span class="font-medium">{{ $supplier->name }}</span>
                                <span class="text-xs text-slate-400">{{ $supplier->phone ?? $supplier->email }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($results['sales']->isNotEmpty())
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Ventes</div>
                    <div class="space-y-1">
                        @foreach ($results['sales'] as $sale)
                            <a class="search-item" href="{{ route('sales.show', $sale) }}" wire:navigate>
                                <span class="font-medium">Vente #{{ $sale->id }}</span>
                                <span class="text-xs text-slate-400">{{ $sale->customer?->name ?? 'Client' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($results['purchases']->isNotEmpty())
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Achats</div>
                    <div class="space-y-1">
                        @foreach ($results['purchases'] as $purchase)
                            <a class="search-item" href="{{ route('purchases.show', $purchase) }}" wire:navigate>
                                <span class="font-medium">Achat #{{ $purchase->id }}</span>
                                <span class="text-xs text-slate-400">{{ $purchase->supplier?->name ?? 'Fournisseur' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
