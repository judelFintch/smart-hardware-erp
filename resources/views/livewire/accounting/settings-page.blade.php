<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-violet-50 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-violet-700">
                    Parametrage comptable
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Choix des comptes imputes par operation</h1>
                <p class="mt-2 text-sm text-slate-500">Le systeme propose des comptes par defaut, mais l administrateur peut choisir les comptes et journaux qui seront imputes automatiquement pour chaque type d operation.</p>
            </div>
            <button wire:click="save" class="btn btn-primary" type="button">Enregistrer</button>
        </div>
        <div class="mt-4 text-sm text-emerald-600" wire:loading.remove wire:target="save" x-data @accounting-settings-saved.window="setTimeout(() => $el.classList.add('hidden'), 2000)">
            Parametrage sauvegarde.
        </div>
    </div>

    @php($grouped = $settings->groupBy('group'))

    @foreach ($grouped as $group => $groupSettings)
        <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $group }}</div>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                @foreach ($groupSettings as $setting)
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4">
                        <label class="block text-sm font-semibold text-slate-900">{{ $setting->label }}</label>
                        <div class="mt-1 text-xs text-slate-500">{{ $setting->description }}</div>
                        @if ($setting->value_type === 'journal')
                            <select wire:model.defer="values.{{ $setting->key }}" class="input mt-3">
                                <option value="">-- Choisir un journal --</option>
                                @foreach ($journals as $journal)
                                    <option value="{{ $journal->id }}">{{ $journal->code }} · {{ $journal->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select wire:model.defer="values.{{ $setting->key }}" class="input mt-3">
                                <option value="">-- Choisir un compte --</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->number }} · {{ $account->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
