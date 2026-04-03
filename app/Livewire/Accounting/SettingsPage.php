<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use App\Models\AccountingSetting;
use App\Models\Journal;
use App\Services\AccountingService;
use Livewire\Component;

class SettingsPage extends Component
{
    public array $values = [];

    public function mount(AccountingService $accounting): void
    {
        $accounting->ensureDefaults();

        $this->values = AccountingSetting::query()
            ->get()
            ->mapWithKeys(fn (AccountingSetting $setting) => [
                $setting->key => $setting->value_type === 'journal'
                    ? (string) ($setting->journal_id ?? '')
                    : (string) ($setting->account_id ?? ''),
            ])
            ->all();
    }

    public function save(): void
    {
        $settings = AccountingSetting::query()->get();

        foreach ($settings as $setting) {
            $field = $setting->value_type === 'journal' ? 'journal_id' : 'account_id';
            $setting->update([
                $field => blank($this->values[$setting->key] ?? null) ? null : (int) $this->values[$setting->key],
            ]);
        }

        $this->dispatch('accounting-settings-saved');
    }

    public function render()
    {
        $settings = AccountingSetting::query()->with(['account', 'journal'])->orderBy('group')->orderBy('label')->get();
        $accounts = Account::query()->orderBy('number')->get();
        $journals = Journal::query()->orderBy('code')->get();

        return view('livewire.accounting.settings-page', compact('settings', 'accounts', 'journals'))
            ->layout('layouts.app');
    }
}
