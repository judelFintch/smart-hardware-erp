<div class="rounded-[24px] border border-amber-200 bg-[linear-gradient(135deg,_#fffdf7_0%,_#fff8eb_100%)] p-4 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-3xl">
            <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-amber-700">
                {{ $eyebrow ?? 'Aide rapide' }}
            </div>
            <h2 class="mt-3 text-lg font-semibold text-slate-900">{{ $title }}</h2>
            @if (!empty($intro))
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $intro }}</p>
            @endif
        </div>
        @if (!empty($actionRoute) && !empty($actionLabel))
            <a href="{{ route($actionRoute) }}" wire:navigate class="btn btn-secondary">{{ $actionLabel }}</a>
        @endif
    </div>

    @if (!empty($items))
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($items as $item)
                <div class="rounded-2xl border border-amber-100 bg-white/80 p-4">
                    <div class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</div>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
