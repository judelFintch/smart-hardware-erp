<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-amber-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                    Corbeille
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Éléments supprimés et restauration</h1>
                <p class="mt-2 text-sm text-slate-500">Toutes les suppressions passent en soft delete. Les articles conservent leurs soldes de stock afin de pouvoir être restaurés proprement.</p>
            </div>
            <div class="w-full max-w-xs">
                <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Type</label>
                <select wire:model.live="type" class="input mt-2">
                    <option value="all">Tous les types</option>
                    @foreach ($types as $key => $config)
                        <option value="{{ $key }}">{{ $config['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Types suivis</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['types']) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Sections visibles</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['visible_types']) }}</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Suppression(s)</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($stats['deleted_total']) }}</div>
        </div>
    </div>

    @if ($sections->isEmpty())
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <x-empty-state
                title="Corbeille vide"
                description="Aucun élément supprimé ne correspond au filtre courant."
            />
        </div>
    @else
        @foreach ($sections as $section)
            <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $section['label'] }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $section['items']->count() }} élément(s) supprimé(s).</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/80">
                            <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                                <th class="px-4 py-3">Élément</th>
                                <th class="px-4 py-3">Infos</th>
                                <th class="px-4 py-3">Supprimé le</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($section['items'] as $item)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-slate-900">
                                            {{ $item->name ?? $item->description ?? $item->reference ?? class_basename($item) . ' #' . $item->id }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">ID {{ $item->id }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        @if ($section['key'] === 'products')
                                            <div>SKU: {{ $item->sku }}</div>
                                            <div class="mt-1">Qté stock: {{ number_format((float) ($item->stock_quantity ?? 0), 3) }}</div>
                                        @elseif ($section['key'] === 'users')
                                            <div>{{ $item->email }}</div>
                                            <div class="mt-1">Rôle: {{ $item->role }}</div>
                                        @elseif ($section['key'] === 'expenses')
                                            <div>{{ number_format((float) $item->amount, 2) }}</div>
                                            <div class="mt-1">{{ $item->category }}</div>
                                        @else
                                            <div>{{ $item->email ?? $item->phone ?? $item->code ?? $item->type ?? '—' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $item->deleted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end">
                                            @if ($section['restorable'])
                                                <button
                                                    wire:click="restore('{{ $section['key'] }}', {{ $item->id }})"
                                                    type="button"
                                                    class="btn btn-secondary"
                                                    data-confirm="Restaurer cet élément supprimé ?"
                                                >
                                                    Restaurer
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
</div>
