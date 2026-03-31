<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-cyan-50 to-slate-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                    Notifications
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Alertes et événements applicatifs</h1>
                <p class="mt-2 text-sm text-slate-500">Suivi des alertes métier et techniques: stock bas, changements de configuration et opérations sensibles.</p>
            </div>
            <button class="btn btn-secondary" type="button" wire:click="markAllAsRead">
                Tout marquer comme lu
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($notifications as $notification)
            @php
                $tone = match ($notification->level) {
                    'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                    'warning' => 'border-amber-200 bg-amber-50 text-amber-700',
                    'error' => 'border-red-200 bg-red-50 text-red-700',
                    default => 'border-cyan-200 bg-cyan-50 text-cyan-700',
                };
            @endphp

            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.14em] {{ $tone }}">
                                {{ $notification->level }}
                            </span>
                            @if (is_null($notification->read_at))
                                <span class="inline-flex rounded-full bg-slate-900 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-white">
                                    Nouveau
                                </span>
                            @endif
                            <span class="text-xs text-slate-400">{{ $notification->created_at?->diffForHumans() }}</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $notification->title }}</h2>
                            @if ($notification->message)
                                <p class="mt-1 text-sm text-slate-600">{{ $notification->message }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if ($notification->link)
                            <a class="btn btn-secondary" href="{{ $notification->link }}" wire:navigate>
                                Ouvrir
                            </a>
                        @endif
                        @if (is_null($notification->read_at))
                            <button class="btn btn-primary" type="button" wire:click="markAsRead({{ $notification->id }})">
                                Marquer comme lu
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-[28px] border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
                <div class="text-lg font-semibold text-slate-900">Aucune notification</div>
                <p class="mt-2 text-sm text-slate-500">Les nouvelles alertes et événements système apparaîtront ici.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $notifications->links() }}
    </div>
</div>
