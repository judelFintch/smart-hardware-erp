<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Services\AccountingService;

class PurchaseOrderObserver
{
    public function created(PurchaseOrder $purchaseOrder): void
    {
        app(AccountingService::class)->postPurchase($purchaseOrder);
    }

    public function updated(PurchaseOrder $purchaseOrder): void
    {
        app(AccountingService::class)->postPurchase($purchaseOrder);
    }

    public function deleted(PurchaseOrder $purchaseOrder): void
    {
        app(AccountingService::class)->forget($purchaseOrder);
    }
}
