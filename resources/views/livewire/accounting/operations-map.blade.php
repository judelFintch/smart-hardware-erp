<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-rose-50 p-6 shadow-sm">
        <div class="max-w-3xl">
            <div class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-rose-700">
                Cartographie comptable
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Comptes appliques sur chaque operation du systeme</h1>
            <p class="mt-2 text-sm text-slate-500">Cette page montre, de facon metier, quelles operations sont comptabilisees, dans quel journal, avec quels comptes debit / credit, et pourquoi.</p>
        </div>
    </div>

    <div class="space-y-4">
        @foreach ($operations as $operation)
            @php
                $isAutomatic = str_contains($operation['status'], 'automatiquement');
                $tone = $isAutomatic ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50';
                $badge = $isAutomatic ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
            @endphp
            <div class="rounded-[24px] border {{ $tone }} p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">{{ $operation['title'] }}</div>
                        <div class="mt-2 text-sm text-slate-600">{{ $operation['reason'] }}</div>
                    </div>
                    <div class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">{{ $operation['status'] }}</div>
                </div>

                <div class="mt-4 grid gap-4 lg:grid-cols-[220px_1fr]">
                    <div class="rounded-2xl bg-white/80 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Journal</div>
                        <div class="mt-2 font-medium text-slate-900">
                            @if ($operation['journal'])
                                {{ $operation['journal']->code }} · {{ $operation['journal']->name }}
                            @else
                                Non applicable
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/80 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Comptes appliques</div>
                        @if (empty($operation['accounts']))
                            <div class="mt-2 text-sm text-slate-500">Aucun compte imputé pour cette operation dans la configuration actuelle.</div>
                        @else
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                @foreach ($operation['accounts'] as $account)
                                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                        <div class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ $account['label'] }}</div>
                                        <div class="mt-1 text-sm font-medium text-slate-900">
                                            @if ($account['value'])
                                                {{ $account['value']->number }} · {{ $account['value']->name }}
                                            @else
                                                Non parametre
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
