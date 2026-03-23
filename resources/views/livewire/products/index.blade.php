<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Catalogue articles
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Produits, stock et accès rapide à la fiche article</h1>
                <p class="mt-2 text-sm text-slate-500">Consultez les quantités disponibles, les prix et ouvrez la fiche de stock détaillée de chaque article.</p>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <form wire:submit.prevent="importCsv" class="flex flex-wrap gap-2 items-center">
                    <input type="file" wire:model="importFile" class="input">
                    <button type="submit" class="btn btn-secondary">Importer fichier</button>
                </form>
                <button type="button" wire:click="downloadImportTemplate" class="btn btn-secondary">Modèle Excel</button>
                <a href="{{ route('products.create') }}" class="btn btn-primary" wire:navigate>Nouveau</a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Articles</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['products_count']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Nombre total de références disponibles.</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Stock affiché</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['stock_total'], 3) }}</div>
            <div class="mt-2 text-sm text-slate-500">
                {{ $selectedLocation ? 'Quantité cumulée pour ' . $selectedLocation->name . '.' : 'Quantité cumulée sur tous les dépôts et magasins.' }}
            </div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Stock critique</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['low_stock_count']) }}</div>
            <div class="mt-2 text-sm text-slate-500">Articles au niveau ou sous le seuil d'alerte.</div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 lg:grid-cols-[1fr_0.8fr_auto] lg:items-center">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Recherche</label>
                <input
                    wire:model.live.debounce.300ms="search"
                    class="input mt-2"
                    placeholder="Nom, SKU ou code-barres"
                >
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Entité</label>
                <select wire:model.live="location_id" class="input mt-2">
                    <option value="">Toutes les entités</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }} ({{ $location->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="text-sm text-slate-500 lg:text-right">
                {{ $products->total() }} article(s) trouvé(s)
            </div>
        </div>
    </div>

    @error('importFile') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
    <div class="text-xs text-slate-500">
        Import accepté: CSV ou Excel. Colonnes attendues: <span class="font-medium text-slate-700">sku, name, barcode, unit_code, description, margin, reorder_level</span>.
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
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Article</th>
                            <th class="px-4 py-3">Références</th>
                            <th class="px-4 py-3">Unité</th>
                            <th class="px-4 py-3 text-right">Qté stock</th>
                            <th class="px-4 py-3 text-right">Coût moyen</th>
                            <th class="px-4 py-3 text-right">Prix vente</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            @php
                                $stock = (float) ($product->filtered_stock_quantity ?? 0);
                                $isLowStock = $stock <= (float) $product->reorder_level;
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">{{ $product->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $product->description ? \Illuminate\Support\Str::limit($product->description, 72) : 'Aucune description' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">
                                    <div>SKU: <span class="font-medium text-slate-900">{{ $product->sku }}</span></div>
                                    <div class="mt-1">Code-barres: {{ $product->barcode ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $product->unit?->code ?? '—' }}</td>
                                <td class="px-4 py-4 text-right">
                                    <div class="text-base font-semibold text-slate-900">{{ number_format($stock, 3) }}</div>
                                    <div class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $isLowStock ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $isLowStock ? 'Stock critique' : 'Stock OK' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right font-medium text-slate-700">{{ number_format((float) $product->avg_cost_local, 2) }}</td>
                                <td class="px-4 py-4 text-right font-medium text-slate-700">{{ number_format((float) $product->sale_price_local, 2) }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('products.stock-card', $product) }}" class="btn btn-secondary" wire:navigate>Fiche stock</a>
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary" wire:navigate>Modifier</a>
                                        <button wire:click="delete({{ $product->id }})" class="btn btn-secondary text-red-600" type="button">Supprimer</button>
                                    </div>
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
</div>
