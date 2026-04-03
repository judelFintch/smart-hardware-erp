<?php

namespace App\Observers;

use App\Models\SalePayment;
use App\Services\AccountingService;

class SalePaymentObserver
{
    public function created(SalePayment $salePayment): void
    {
        app(AccountingService::class)->postSalePayment($salePayment);
    }

    public function updated(SalePayment $salePayment): void
    {
        app(AccountingService::class)->postSalePayment($salePayment);
    }

    public function deleted(SalePayment $salePayment): void
    {
        app(AccountingService::class)->forget($salePayment);
    }
}
