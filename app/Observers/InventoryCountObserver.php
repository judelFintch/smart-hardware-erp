<?php

namespace App\Observers;

use App\Models\InventoryCount;
use App\Services\AccountingService;

class InventoryCountObserver
{
    public function created(InventoryCount $inventoryCount): void
    {
        app(AccountingService::class)->postInventoryCount($inventoryCount);
    }

    public function updated(InventoryCount $inventoryCount): void
    {
        app(AccountingService::class)->postInventoryCount($inventoryCount);
    }

    public function deleted(InventoryCount $inventoryCount): void
    {
        app(AccountingService::class)->forget($inventoryCount);
    }
}
