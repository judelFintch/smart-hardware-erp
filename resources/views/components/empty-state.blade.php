@props([
    'title' => 'Aucune donnée',
    'description' => null,
    'action' => null,
    'actionHref' => null,
])

<div class="empty-state">
    <div class="text-lg font-semibold text-slate-700">{{ $title }}</div>
    @if ($description)
        <div class="text-sm text-slate-400">{{ $description }}</div>
    @endif
    @if ($action && $actionHref)
        <a class="btn btn-primary mt-2" href="{{ $actionHref }}" wire:navigate>{{ $action }}</a>
    @endif
</div>
