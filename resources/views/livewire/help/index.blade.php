<div id="top" class="space-y-6">
    <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.18),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(34,211,238,0.16),_transparent_24%),linear-gradient(135deg,_#ffffff_0%,_#fff9ed_45%,_#f7fcff_100%)] p-6 shadow-sm">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center rounded-full border border-amber-200 bg-white/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-amber-700 shadow-sm backdrop-blur">
                        Centre d'aide
                    </div>
                    <h1 class="mt-4 max-w-2xl text-3xl font-semibold leading-tight tracking-[-0.02em] text-slate-900 md:text-4xl">
                        Trouvez vite ce que vous cherchez, sans lire tout le manuel
                    </h1>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
                        Le centre d’aide vous guide selon vos besoins réels: comprendre une fonctionnalité, résoudre un blocage, ou ouvrir directement la bonne page pour agir.
                    </p>
                </div>
                <div class="max-w-sm rounded-[28px] border border-white/80 bg-white/85 p-5 text-sm text-slate-600 shadow-sm backdrop-blur">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Démarrage recommandé</div>
                    <div class="mt-3 text-base font-semibold leading-6 text-slate-900">Suivez le bon ordre pour éviter les erreurs de configuration.</div>
                    <div class="mt-3 rounded-2xl bg-slate-50 px-4 py-3 leading-6 text-slate-600">
                        Entreprise, unités, emplacements, produits, achats, puis ventes.
                    </div>
                </div>
            </div>

            <div class="grid gap-3 lg:grid-cols-[1fr_220px]">
                <label class="block">
                    <span class="sr-only">Rechercher dans l'aide</span>
                    <input
                        wire:model.live.debounce.250ms="search"
                        type="search"
                        class="input h-14 rounded-2xl border-white/80 bg-white/90 px-5 text-base shadow-sm backdrop-blur placeholder:text-slate-400"
                        placeholder="Exemple: vente, stock, inventaire, utilisateur, achat..."
                    >
                </label>
                <div class="flex items-center rounded-2xl border border-white/80 bg-white/85 px-4 py-3 text-sm text-slate-600 shadow-sm backdrop-blur">
                    <span class="text-2xl font-semibold tracking-[-0.03em] text-slate-900">{{ $sections->count() }}</span>
                    <span class="ml-2 leading-5">section(s) trouvée(s)</span>
                </div>
            </div>
        </div>
    </div>

    <section class="grid gap-4 lg:grid-cols-3">
        @foreach ($smartGuides as $guide)
            <a href="#{{ $guide['anchor'] }}" class="group rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
                <div class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.15em] text-slate-500">Je veux</div>
                <div class="mt-4 text-lg font-semibold leading-6 text-slate-900 transition group-hover:text-cyan-800">{{ $guide['title'] }}</div>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $guide['description'] }}</p>
                <div class="mt-5 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700">
                    <span>Lire cette aide</span>
                    <span aria-hidden="true">→</span>
                </div>
            </a>
        @endforeach
    </section>

    @if ($quickActions->isNotEmpty())
        <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    Actions rapides
                </div>
                <div class="text-sm text-slate-500">Allez directement à l’action</div>
            </div>
            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($quickActions as $action)
                    <a href="{{ route($action['route']) }}" wire:navigate class="group rounded-[24px] border border-slate-200 bg-[linear-gradient(180deg,_#ffffff_0%,_#f8fafc_100%)] p-5 transition duration-200 hover:border-emerald-200 hover:shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="font-semibold leading-6 text-slate-900">{{ $action['title'] }}</div>
                            <div class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-700">Direct</div>
                        </div>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $action['description'] }}</p>
                        <div class="mt-5 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">
                            <span>Ouvrir</span>
                            <span aria-hidden="true">→</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($onboardingSteps->isNotEmpty())
        <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-700">
                        Checklist de démarrage
                    </div>
                    <div class="mt-3 text-xl font-semibold text-slate-900">Vérifiez rapidement si votre système est prêt</div>
                </div>
            </div>
            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($onboardingSteps as $step)
                    <a href="{{ route($step['route']) }}" wire:navigate class="rounded-[24px] border {{ $step['done'] ? 'border-emerald-200 bg-emerald-50/60' : 'border-slate-200 bg-slate-50/70' }} p-5 transition hover:shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="font-semibold leading-6 text-slate-900">{{ $step['title'] }}</div>
                            <div class="rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] {{ $step['done'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                {{ $step['done'] ? 'Fait' : 'À faire' }}
                            </div>
                        </div>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $step['description'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($faqItems->isNotEmpty())
        <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <div class="inline-flex items-center rounded-full bg-violet-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-violet-700">
                    FAQ intelligente
                </div>
                <div class="mt-3 text-xl font-semibold text-slate-900">Questions fréquentes des utilisateurs</div>
            </div>
            <div class="mt-5 space-y-3">
                @foreach ($faqItems as $item)
                    <div x-data="{ open: false }" class="overflow-hidden rounded-[22px] border border-slate-200 bg-slate-50/60">
                        <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                            <span class="font-semibold text-slate-900">{{ $item['question'] }}</span>
                            <span class="text-slate-400" x-text="open ? '−' : '+'"></span>
                        </button>
                        <div x-show="open" x-collapse class="border-t border-slate-200 bg-white px-5 py-4">
                            <p class="text-sm leading-7 text-slate-600">{{ $item['answer'] }}</p>
                            @if (!empty($item['anchor']))
                                <a href="#{{ $item['anchor'] }}" class="mt-3 inline-flex text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700">
                                    Lire le détail
                                </a>
                            @elseif (!empty($item['route']))
                                <a href="{{ route($item['route']) }}" wire:navigate class="mt-3 inline-flex text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700">
                                    Ouvrir la page utile
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid gap-6 xl:grid-cols-[280px_1fr]">
        <aside class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm xl:sticky xl:top-20 xl:self-start">
            <div class="px-2">
                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Sommaire</div>
                <div class="mt-2 text-sm leading-6 text-slate-500">Chaque section résume une fonctionnalité et vous dirige vers la bonne action.</div>
            </div>
            <nav class="mt-4 space-y-2">
                @forelse ($sections as $section)
                    <a href="#{{ $section['anchor'] }}" class="block rounded-2xl border border-transparent px-3 py-3 text-sm text-slate-600 transition hover:border-slate-200 hover:bg-slate-50 hover:text-slate-900">
                        <div class="font-semibold leading-6 text-slate-900">{{ $section['title'] }}</div>
                        @if ($section['summary'] !== '')
                            <div class="mt-1.5 text-xs leading-5 text-slate-500">{{ $section['summary'] }}</div>
                        @endif
                    </a>
                @empty
                    <div class="rounded-2xl bg-slate-50 px-3 py-3 text-sm text-slate-500">
                        Aucun résultat pour cette recherche.
                    </div>
                @endforelse
            </nav>
        </aside>

        <div class="space-y-6">
            @forelse ($sections as $section)
                <section id="{{ $section['anchor'] }}" class="overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-sm scroll-mt-24">
                    <div class="border-b border-slate-100 bg-[linear-gradient(180deg,_#ffffff_0%,_#fbfdff_100%)] px-6 py-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-3xl">
                                <div class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Guide pratique</div>
                                <h2 class="mt-3 text-2xl font-semibold leading-tight tracking-[-0.02em] text-slate-900">{{ $section['title'] }}</h2>
                            @if ($section['summary'] !== '')
                                <p class="mt-3 text-base leading-7 text-slate-600">{{ $section['summary'] }}</p>
                            @endif
                            </div>
                            <div class="flex gap-2">
                                @if ($section['route'])
                                    <a href="{{ route($section['route']) }}" wire:navigate class="btn btn-secondary">Ouvrir le module</a>
                                @endif
                                <a href="#top" class="btn btn-secondary">Haut</a>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-6">
                        <div class="prose prose-slate prose-lg max-w-none prose-headings:font-semibold prose-headings:text-slate-900 prose-p:leading-8 prose-p:text-slate-600 prose-li:leading-8 prose-li:text-slate-600 prose-strong:text-slate-900 prose-ul:space-y-2 prose-ol:space-y-2">
                            {!! $section['html'] !!}
                        </div>
                    </div>
                </section>
            @empty
                <div class="rounded-[28px] border border-slate-200 bg-white p-10 text-center shadow-sm">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-xl">?</div>
                    <div class="mt-4 text-xl font-semibold text-slate-900">Aucune aide trouvée</div>
                    <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-slate-600">
                        Essayez un mot plus simple comme <span class="font-medium">vente</span>, <span class="font-medium">achat</span>, <span class="font-medium">stock</span> ou <span class="font-medium">utilisateur</span>.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
