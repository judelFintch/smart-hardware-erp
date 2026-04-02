<div class="space-y-6">
    <div class="rounded-[26px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-5 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Catalogue articles
                </div>
                <h1 class="mt-2.5 text-2xl font-semibold text-slate-900">Produits, stock et accès rapide à la fiche article</h1>
                <p class="mt-1.5 text-sm text-slate-500">Consultez les quantités disponibles, les prix et ouvrez la fiche de stock détaillée de chaque article.</p>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <form wire:submit.prevent="importCsv" class="flex flex-wrap gap-2 items-center">
                    <select wire:model="import_location_id" class="input" @disabled(!$canSelectAnyLocation)>
                        <option value="">Entité du stock</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                        @endforeach
                    </select>
                    <input type="file" wire:model="importFile" class="input">
                    <button type="submit" class="btn btn-secondary">Importer fichier</button>
                </form>
                <button type="button" wire:click="downloadImportTemplate" class="btn btn-secondary">Modèle Excel</button>
                <a href="{{ route('products.create') }}" class="btn btn-primary" wire:navigate>Nouveau</a>
            </div>
        </div>
    </div>

    <div class="rounded-[24px] border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
        <div class="grid gap-x-4 gap-y-1.5 md:grid-cols-3">
            <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 md:border-b-0 md:border-r md:pr-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Articles</span>
                <span class="shrink-0 text-sm font-semibold text-slate-900">{{ number_format($stats['products_count']) }}</span>
            </div>
            <div class="flex min-w-0 items-baseline justify-between gap-2 border-b border-slate-100 py-1.5 md:border-b-0 md:border-r md:px-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Stock affiché</span>
                <span class="shrink-0 text-sm font-semibold text-slate-900">{{ $this->formatQuantity($stats['stock_total']) }}</span>
            </div>
            <div class="flex min-w-0 items-baseline justify-between gap-2 py-1.5 md:pl-3">
                <span class="truncate text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Stock critique</span>
                <span class="shrink-0 text-sm font-semibold text-slate-900">{{ number_format($stats['low_stock_count']) }}</span>
            </div>
        </div>
        <div class="mt-2 text-xs text-slate-500">
            {{ $selectedLocation ? 'Entité filtrée: ' . $selectedLocation->name . '.' : 'Vue globale sur tous les dépôts et magasins.' }}
        </div>
    </div>

    <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 lg:grid-cols-[1fr_0.8fr_140px_auto] lg:items-end">
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Recherche</label>
                <input
                    wire:model.live.debounce.300ms="search"
                    class="input mt-2"
                    placeholder="Nom, SKU ou code-barres"
                >
            </div>
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Entité</label>
                <select wire:model.live="location_id" class="input mt-2" @disabled(!$canSelectAnyLocation)>
                    <option value="">Toutes les entités</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Lignes</label>
                <select wire:model.live="perPage" class="input mt-2">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="text-sm text-slate-500 lg:text-right">
                {{ $products->total() }} article(s) trouve(s)
            </div>
        </div>
    </div>

    @include('livewire.partials.context-help', [
        'eyebrow' => 'Aide Produits',
        'title' => 'Comment bien utiliser cette page',
        'intro' => 'Utilisez cette page pour retrouver un article, contrôler son stock et importer rapidement un catalogue. Si vous cherchez l’historique complet d’un produit, ouvrez sa fiche stock.',
        'items' => [
            ['title' => 'Créer un article', 'text' => 'Cliquez sur Nouveau pour ajouter manuellement un produit avec son unité, son prix et son seuil.'],
            ['title' => 'Importer un fichier', 'text' => 'Choisissez d’abord une entité de stock, puis importez votre CSV ou Excel pour injecter les quantités initiales.'],
            ['title' => 'Contrôler le stock', 'text' => 'Utilisez la colonne Qté et le badge Stock critique pour repérer rapidement les articles à réapprovisionner.'],
        ],
        'actionRoute' => 'help.index',
        'actionLabel' => 'Voir toute l’aide',
    ])

    @error('importFile') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
    @error('import_location_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
    <div class="text-xs text-slate-500">
        Import accepté: CSV ou Excel. Colonnes attendues: <span class="font-medium text-slate-700">sku, name, barcode, unit_code, description, cost, price, stock, margin, reorder_level</span>.
        Si `stock` est renseigné, il sera ajouté dans l'entité sélectionnée.
    </div>
    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($products->isEmpty())
            <x-empty-state
                title="Aucun article"
                description="Créez votre premier article pour démarrer la gestion du stock."
                action="Nouvel article"
                :action-href="route('products.create')"
            />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-[11px] uppercase tracking-[0.14em] text-slate-500">
                            <th class="px-5 py-3">
                                <button wire:click="sortBy('name')" type="button" class="inline-flex items-center gap-1 font-semibold">
                                    Article
                                    @if ($sortField === 'name') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                                </button>
                            </th>
                            <th class="px-4 py-3">
                                <button wire:click="sortBy('sku')" type="button" class="inline-flex items-center gap-1 font-semibold">
                                    Références
                                    @if ($sortField === 'sku') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                                </button>
                            </th>
                            <th class="px-3 py-3">Unité</th>
                            <th class="px-3 py-3 text-right">
                                <button wire:click="sortBy('filtered_stock_quantity')" type="button" class="inline-flex items-center justify-end gap-1 font-semibold">
                                    Qté
                                    @if ($sortField === 'filtered_stock_quantity') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                                </button>
                            </th>
                            <th class="px-3 py-3 text-right">
                                <button wire:click="sortBy('avg_cost_local')" type="button" class="inline-flex items-center justify-end gap-1 font-semibold">
                                    PU
                                    @if ($sortField === 'avg_cost_local') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                                </button>
                            </th>
                            <th class="px-3 py-3 text-right">
                                <button wire:click="sortBy('sale_price_local')" type="button" class="inline-flex items-center justify-end gap-1 font-semibold">
                                    PV
                                    @if ($sortField === 'sale_price_local') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                                </button>
                            </th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            @php
                                $stock = (float) ($product->filtered_stock_quantity ?? 0);
                                $isLowStock = $stock <= (float) $product->reorder_level;
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <div class="font-medium leading-5 text-slate-900">{{ $product->name }}</div>
                                    <div class="mt-1 text-[11px] text-slate-500">
                                        {{ $product->description ? \Illuminate\Support\Str::limit($product->description, 72) : 'Aucune description' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">
                                    <div>SKU: <span class="font-medium text-slate-900">{{ $product->sku }}</span></div>
                                    <div class="mt-1 text-[11px]">Code-barres: {{ $product->barcode ?? '—' }}</div>
                                </td>
                                <td class="px-3 py-4 text-sm text-slate-600">{{ $product->unit?->code ?? '—' }}</td>
                                <td class="px-3 py-4 text-right">
                                    <div class="text-sm font-semibold text-slate-900">{{ $this->formatQuantity($stock) }}</div>
                                    <div class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $isLowStock ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $isLowStock ? 'Stock critique' : 'Stock OK' }}
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format((float) $product->avg_cost_local, 2) }}</td>
                                <td class="px-3 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format((float) $product->sale_price_local, 2) }}</td>
                                <td class="px-5 py-4 text-right">
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button type="button" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] text-slate-600 shadow-sm hover:bg-slate-50">
                                                Actions
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            <x-dropdown-link href="{{ route('products.stock-card', $product) }}" wire:navigate>
                                                Fiche stock
                                            </x-dropdown-link>
                                            <x-dropdown-link href="{{ route('products.edit', $product) }}" wire:navigate>
                                                Modifier
                                            </x-dropdown-link>
                                            <button
                                                wire:click='openDeleteModal({{ $product->id }}, @json($product->name))'
                                                type="button"
                                                class="block w-full px-4 py-2 text-left text-sm leading-5 text-red-600 transition duration-150 ease-in-out hover:bg-red-50 focus:bg-red-50 focus:outline-none"
                                            >
                                                Supprimer
                                            </button>
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div>
        {{ $products->links() }}
    </div>

    @include('livewire.partials.delete-secret-modal')
</div>
