<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $expenseId): void
    {
        Expense::whereKey($expenseId)->delete();
    }

    public function render()
    {
        $expenses = Expense::orderByDesc('spent_at')->get();

        return view('livewire.expenses.index', compact('expenses'))
            ->layout('layouts.app');
    }
}
