<?php

namespace App\Providers;

use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\StockLocation;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Observers\ActivityObserver;
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
            Sale::class,
            Expense::class,
            User::class,
            StockLocation::class,
            Unit::class,
            CompanySetting::class,
            InventoryCount::class,
        ];

        foreach ($models as $model) {
            $model::observe(ActivityObserver::class);
        }
    }
}
