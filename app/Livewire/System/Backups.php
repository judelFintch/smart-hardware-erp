<?php

namespace App\Livewire\System;

use App\Models\ActivityLog;
use App\Models\AppNotification;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Livewire\Component;

class Backups extends Component
{
    public function downloadSnapshot()
    {
        $payload = [
            'generated_at' => now()->toIso8601String(),
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
            ],
            'company' => CompanySetting::query()->first(),
            'users' => $this->exportCollection(User::class),
            'products' => $this->exportCollection(Product::class),
            'customers' => $this->exportCollection(Customer::class),
            'suppliers' => $this->exportCollection(Supplier::class),
            'stock_locations' => $this->exportCollection(StockLocation::class),
            'stock_balances' => $this->exportCollection(StockBalance::class),
            'purchases' => $this->exportCollection(PurchaseOrder::class),
            'sales' => $this->exportCollection(Sale::class),
            'expenses' => $this->exportCollection(Expense::class),
            'units' => $this->exportCollection(Unit::class),
            'notifications' => AppNotification::query()->get(),
            'activity_logs' => ActivityLog::query()->latest()->limit(1000)->get(),
        ];

        $filename = 'snapshot-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function render()
    {
        $stats = [
            'users' => User::count(),
            'products' => Product::count(),
            'sales' => Sale::count(),
            'purchases' => PurchaseOrder::count(),
            'notifications' => AppNotification::query()->count(),
        ];

        return view('livewire.system.backups', compact('stats'))
            ->layout('layouts.app');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    private function exportCollection(string $modelClass)
    {
        $query = $modelClass::query();

        if (in_array(SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            $query->withTrashed();
        }

        return $query->get();
    }
}
