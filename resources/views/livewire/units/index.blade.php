<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Unités</h1>
            <p class="text-sm text-slate-500">Gestion des unités (pièce, kg, litre, etc.).</p>
        </div>
        <a href="{{ route('units.create') }}" class="btn btn-primary" wire:navigate>Nouvelle unité</a>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-3">Code</th>
                    <th class="p-3">Nom</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($units as $unit)
                    <tr class="border-b">
                        <td class="p-3">{{ strtoupper($unit->code) }}</td>
                        <td class="p-3">{{ $unit->name }}</td>
                        <td class="p-3">
                            <span class="badge badge-info">{{ $unit->type }}</span>
                        </td>
                        <td class="p-3">
                            <a href="{{ route('units.edit', $unit) }}" class="text-blue-600" wire:navigate>Modifier</a>
                            <button wire:click="delete({{ $unit->id }})" class="text-red-600 ml-2" type="button">Supprimer</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
