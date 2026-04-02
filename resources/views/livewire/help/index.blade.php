<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-amber-50 to-cyan-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                    Centre d'aide
                </div>
                <h1 class="mt-3 text-2xl font-semibold text-slate-900">Guide d'utilisation du système</h1>
                <p class="mt-2 text-sm text-slate-600">
                    Cette page explique le fonctionnement général de l'application et donne un mode d'emploi pratique pour les principales fonctionnalités.
                </p>
            </div>
            <div class="rounded-2xl border border-white/70 bg-white/80 p-4 text-sm text-slate-600 shadow-sm">
                <div class="font-semibold text-slate-900">Conseil</div>
                <div class="mt-1">Commencez par configurer l'entreprise, les unités, les emplacements, puis les produits avant les opérations quotidiennes.</div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[280px_1fr]">
        <aside class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm xl:sticky xl:top-20 xl:self-start">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Sommaire</div>
            <nav class="mt-4 space-y-1">
                @foreach ($sections as $section)
                    <a href="#{{ $section['anchor'] }}" class="block rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                        {{ $section['title'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="space-y-5">
            @foreach ($sections as $section)
                <section id="{{ $section['anchor'] }}" class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm scroll-mt-24">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $section['title'] }}</h2>
                    <div class="prose prose-slate mt-4 max-w-none prose-headings:text-slate-900 prose-p:text-slate-600 prose-li:text-slate-600 prose-strong:text-slate-900">
                        {!! $section['html'] !!}
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>
