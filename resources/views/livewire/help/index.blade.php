<div id="top" class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-amber-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                        Centre d'aide
                    </div>
                    <h1 class="mt-3 text-2xl font-semibold text-slate-900">Trouvez vite ce que vous voulez faire</h1>
                    <p class="mt-2 text-sm text-slate-600">
                        Recherchez une tâche, ouvrez une action rapide ou lisez une explication simple avant d'aller dans le détail.
                    </p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 text-sm text-slate-600 shadow-sm">
                    <div class="font-semibold text-slate-900">Point de départ conseillé</div>
                    <div class="mt-1">Entreprise, unités, emplacements, produits, achats, puis ventes. Cet ordre évite la plupart des erreurs de départ.</div>
                </div>
            </div>

            <div class="grid gap-3 lg:grid-cols-[1fr_220px]">
                <label class="block">
                    <span class="sr-only">Rechercher dans l'aide</span>
                    <input
                        wire:model.live.debounce.250ms="search"
                        type="search"
                        class="input h-12"
                        placeholder="Exemple: vente, stock, inventaire, utilisateur, achat..."
                    >
                </label>
                <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 text-sm text-slate-600 shadow-sm">
                    <span class="font-semibold text-slate-900">{{ $sections->count() }}</span> section(s) trouvée(s)
                </div>
            </div>
        </div>
    </div>

    <section class="grid gap-4 lg:grid-cols-3">
        @foreach ($smartGuides as $guide)
            <a href="#{{ $guide['anchor'] }}" class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
                <div class="text-sm font-semibold text-slate-900">{{ $guide['title'] }}</div>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $guide['description'] }}</p>
                <div class="mt-4 text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700">Lire cette aide</div>
            </a>
        @endforeach
    </section>

    @if ($quickActions->isNotEmpty())
        <section class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                    Actions rapides
                </div>
                <div class="text-sm text-slate-500">Accès direct aux tâches les plus fréquentes</div>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($quickActions as $action)
                    <a href="{{ route($action['route']) }}" wire:navigate class="rounded-[22px] border border-slate-200 bg-slate-50/70 p-4 transition hover:bg-white hover:shadow-sm">
                        <div class="font-semibold text-slate-900">{{ $action['title'] }}</div>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $action['description'] }}</p>
                        <div class="mt-4 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">Ouvrir</div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid gap-6 xl:grid-cols-[280px_1fr]">
        <aside class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm xl:sticky xl:top-20 xl:self-start">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Sommaire</div>
            <nav class="mt-4 space-y-1">
                @forelse ($sections as $section)
                    <a href="#{{ $section['anchor'] }}" class="block rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                        <div class="font-medium text-slate-900">{{ $section['title'] }}</div>
                        @if ($section['summary'] !== '')
                            <div class="mt-1 text-xs leading-5 text-slate-500">{{ $section['summary'] }}</div>
                        @endif
                    </a>
                @empty
                    <div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">
                        Aucun résultat pour cette recherche.
                    </div>
                @endforelse
            </nav>
        </aside>

        <div class="space-y-5">
            @forelse ($sections as $section)
                <section id="{{ $section['anchor'] }}" class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm scroll-mt-24">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $section['title'] }}</h2>
                            @if ($section['summary'] !== '')
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $section['summary'] }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            @if ($section['route'])
                                <a href="{{ route($section['route']) }}" wire:navigate class="btn btn-secondary">Ouvrir le module</a>
                            @endif
                            <a href="#top" class="btn btn-secondary">Haut</a>
                        </div>
                    </div>
                    <div class="prose prose-slate mt-5 max-w-none prose-headings:text-slate-900 prose-p:text-slate-600 prose-li:text-slate-600 prose-strong:text-slate-900">
                        {!! $section['html'] !!}
                    </div>
                </section>
            @empty
                <div class="rounded-[24px] border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <div class="text-lg font-semibold text-slate-900">Aucune aide trouvée</div>
                    <p class="mt-2 text-sm text-slate-600">
                        Essayez un mot plus simple comme <span class="font-medium">vente</span>, <span class="font-medium">achat</span>, <span class="font-medium">stock</span> ou <span class="font-medium">utilisateur</span>.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
