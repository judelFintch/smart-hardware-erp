<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryCountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalePaymentController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('products', ProductController::class)->except(['show']);
Route::resource('suppliers', SupplierController::class)->except(['show']);
Route::resource('customers', CustomerController::class)->except(['show']);
Route::resource('expenses', ExpenseController::class)->except(['show']);
Route::get('inventory-counts/create', [InventoryCountController::class, 'create'])->name('inventory-counts.create');
Route::post('inventory-counts', [InventoryCountController::class, 'store'])->name('inventory-counts.store');

Route::get('purchases', [PurchaseOrderController::class, 'index'])->name('purchases.index');
Route::get('purchases/export', [PurchaseOrderController::class, 'export'])->name('purchases.export');
Route::get('purchases/create', [PurchaseOrderController::class, 'create'])->name('purchases.create');
Route::post('purchases', [PurchaseOrderController::class, 'store'])->name('purchases.store');
Route::get('purchases/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchases.show');
Route::post('purchases/{purchaseOrder}/transfers', [PurchaseOrderController::class, 'storeTransfer'])->name('purchases.transfers.store');
Route::post('purchases/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchases.receive');

Route::get('stock-transfers/create', [StockTransferController::class, 'create'])->name('stock-transfers.create');
Route::post('stock-transfers', [StockTransferController::class, 'store'])->name('stock-transfers.store');

Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('sales/export', [SaleController::class, 'export'])->name('sales.export');
Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
Route::post('sales/{sale}/returns', [SaleController::class, 'returnItem'])->name('sales.returns.store');

Route::post('sales/{sale}/payments', [SalePaymentController::class, 'store'])->name('sales.payments.store');

Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
