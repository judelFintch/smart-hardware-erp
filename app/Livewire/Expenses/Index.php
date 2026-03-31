<?php

namespace App\Livewire\Expenses;

use App\Livewire\Concerns\ConfirmsDeletionWithSecretCode;
use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use ConfirmsDeletionWithSecretCode, WithPagination;

    public int $perPage = 15;

    protected function performDelete(int $expenseId): void
    {
        Expense::whereKey($expenseId)->delete();
    }

    public function render()
    {
        $expenses = Expense::orderByDesc('spent_at')->paginate($this->perPage);

        return view('livewire.expenses.index', compact('expenses'))
            ->layout('layouts.app');
    }
}
