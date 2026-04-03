<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;

class AccountsIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 20;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $accounts = Account::query()
            ->when(trim($this->search) !== '', function ($query) {
                $like = '%' . trim($this->search) . '%';
                $query->where(fn ($inner) => $inner
                    ->where('number', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('category', 'like', $like));
            })
            ->orderBy('number')
            ->paginate($this->perPage);

        return view('livewire.accounting.accounts-index', compact('accounts'))
            ->layout('layouts.app');
    }
}
