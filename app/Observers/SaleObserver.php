<?php

namespace App\Observers;

use App\Models\Sale;
use App\Services\AccountingService;

class SaleObserver
{
    public function created(Sale $sale): void
    {
        app(AccountingService::class)->postSale($sale);
    }

    public function updated(Sale $sale): void
    {
        app(AccountingService::class)->postSale($sale);
    }

    public function deleted(Sale $sale): void
    {
        app(AccountingService::class)->forget($sale);
    }
}
