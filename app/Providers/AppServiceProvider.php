<?php

namespace App\Providers;

use App\Listeners\SendLoginAlert;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Attachment;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseTransfer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Observers\ActivityObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, SendLoginAlert::class);

        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        $models = [
            Product::class,
            Customer::class,
            Supplier::class,
            PurchaseOrder::class,
            PurchaseOrderItem::class,
            PurchaseTransfer::class,
            Sale::class,
            SaleItem::class,
            Expense::class,
            User::class,
            StockLocation::class,
            StockMovement::class,
            Unit::class,
            CompanySetting::class,
            InventoryCount::class,
            InventoryCountItem::class,
            Attachment::class,
        ];

        foreach ($models as $model) {
            $model::observe(ActivityObserver::class);
        }
    }
}
