<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Livewire\Component;

class Form extends Component
{
    public ?Expense $expense = null;
    public string $category = '';
    public string $description = '';
    public float $amount = 0;
    public ?string $spent_at = null;
    public string $reference = '';

    public function mount(?Expense $expense = null): void
    {
        if ($expense && $expense->exists) {
            $this->expense = $expense;
            $this->category = (string) $expense->category;
            $this->description = $expense->description;
            $this->amount = (float) $expense->amount;
            $this->spent_at = $expense->spent_at?->toDateString();
            $this->reference = (string) $expense->reference;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent_at' => ['nullable', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->expense) {
            $this->expense->update($data);
        } else {
            Expense::create($data);
        }

        $this->redirectRoute('expenses.index');
    }

    public function render()
    {
        $title = $this->expense ? 'Modifier Dépense' : 'Nouvelle Dépense';

        return view('livewire.expenses.form', compact('title'))
            ->layout('layouts.app');
    }
}
