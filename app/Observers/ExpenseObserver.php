<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\AccountingService;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        app(AccountingService::class)->postExpense($expense);
    }

    public function updated(Expense $expense): void
    {
        app(AccountingService::class)->postExpense($expense);
    }

    public function deleted(Expense $expense): void
    {
        app(AccountingService::class)->forget($expense);
    }
}
