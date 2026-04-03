<div class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-amber-50 p-6 shadow-sm">
        <div class="max-w-3xl">
            <div class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                Comptabilite
            </div>
            <h1 class="mt-3 text-3xl font-semibold text-slate-900">Plan comptable visible dans le systeme</h1>
            <p class="mt-2 text-sm text-slate-500">Consulte tous les comptes disponibles pour l imputation automatique et le paramétrage SYSCOHADA.</p>
        </div>
    </div>

    <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm">
        <label class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Recherche</label>
        <input wire:model.live.debounce.300ms="search" class="input mt-2" placeholder="Numero, nom ou categorie">
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($accounts->isEmpty())
            <div class="px-6 py-10 text-sm text-slate-500">Aucun compte comptable disponible.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <th class="px-4 py-3">Numero</th>
                            <th class="px-4 py-3">Libelle</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Categorie</th>
                            <th class="px-4 py-3">Comportement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($accounts as $account)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4 font-semibold text-slate-900">{{ $account->number }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $account->name }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $account->type }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $account->category ?: '—' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-500">
                                    {{ $account->is_system ? 'Compte propose par defaut par le systeme, modifiable via le parametrage comptable.' : 'Compte manuel disponible pour les imputations choisies par l admin.' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div>{{ $accounts->links() }}</div>
</div>
