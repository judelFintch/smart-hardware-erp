<?php

use App\Livewire\Customers\Form as CustomersForm;
use App\Livewire\Customers\Index as CustomersIndex;
use App\Livewire\Dashboard;
use App\Livewire\Expenses\Form as ExpensesForm;
use App\Livewire\Expenses\Index as ExpensesIndex;
use App\Livewire\InventoryCounts\Create as InventoryCountsCreate;
use App\Livewire\Products\Form as ProductsForm;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Purchases\Create as PurchasesCreate;
use App\Livewire\Purchases\Index as PurchasesIndex;
use App\Livewire\Purchases\Show as PurchasesShow;
use App\Livewire\Reports\Financial as ReportsFinancial;
use App\Livewire\Sales\Create as SalesCreate;
use App\Livewire\Sales\Index as SalesIndex;
use App\Livewire\Sales\Show as SalesShow;
use App\Livewire\StockTransfers\Create as StockTransfersCreate;
use App\Livewire\Suppliers\Form as SuppliersForm;
use App\Livewire\Suppliers\Index as SuppliersIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('products', ProductsIndex::class)->name('products.index');
    Route::get('products/create', ProductsForm::class)->middleware('role:owner,manager')->name('products.create');
    Route::get('products/{product}/edit', ProductsForm::class)->middleware('role:owner,manager')->name('products.edit');

    Route::get('suppliers', SuppliersIndex::class)->name('suppliers.index');
    Route::get('suppliers/create', SuppliersForm::class)->name('suppliers.create');
    Route::get('suppliers/{supplier}/edit', SuppliersForm::class)->name('suppliers.edit');

    Route::get('customers', CustomersIndex::class)->name('customers.index');
    Route::get('customers/create', CustomersForm::class)->name('customers.create');
    Route::get('customers/{customer}/edit', CustomersForm::class)->name('customers.edit');

    Route::get('expenses', ExpensesIndex::class)->name('expenses.index');
    Route::get('expenses/create', ExpensesForm::class)->name('expenses.create');
    Route::get('expenses/{expense}/edit', ExpensesForm::class)->name('expenses.edit');

    Route::get('purchases', PurchasesIndex::class)->name('purchases.index');
    Route::get('purchases/create', PurchasesCreate::class)->name('purchases.create');
    Route::get('purchases/{purchaseOrder}', PurchasesShow::class)->name('purchases.show');

    Route::get('stock-transfers/create', StockTransfersCreate::class)->name('stock-transfers.create');

    Route::get('sales', SalesIndex::class)->name('sales.index');
    Route::get('sales/create', SalesCreate::class)->name('sales.create');
    Route::get('sales/{sale}', SalesShow::class)->name('sales.show');

    Route::get('inventory-counts/create', InventoryCountsCreate::class)->name('inventory-counts.create');

    Route::get('reports/financial', ReportsFinancial::class)->middleware('role:owner,manager')->name('reports.financial');
});

require __DIR__.'/auth.php';
