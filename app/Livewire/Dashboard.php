<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Support\LocationAccess;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Dashboard extends Component
{
    public string $period = 'daily';

    private function periodStart()
    {
        return match ($this->period) {
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => now()->startOfDay(),
        };
    }

    public function render()
    {
        $start = $this->periodStart();
        $end = now()->endOfDay();
        $locationId = LocationAccess::assignedLocationId();

        $kpis = Cache::remember("dashboard:kpis:{$this->period}:{$locationId}", now()->addMinutes(1), function () use ($start, $end) {
            $salesTotal = LocationAccess::filterSales(Sale::query())
                ->whereBetween('sold_at', [$start, $end])
                ->sum('total_amount');
            $cogsTotal = SaleItem::query()
                ->when(!LocationAccess::hasGlobalAccess(), fn ($query) => $query->where('location_id', LocationAccess::assignedLocationId()))
                ->whereHas('sale', fn ($q) => $q->whereBetween('sold_at', [$start, $end]))
                ->selectRaw('SUM(quantity * unit_cost_local) as cogs')
                ->value('cogs') ?? 0;
            $salesRevenue = SaleItem::query()
                ->when(!LocationAccess::hasGlobalAccess(), fn ($query) => $query->where('location_id', LocationAccess::assignedLocationId()))
                ->whereHas('sale', fn ($q) => $q->whereBetween('sold_at', [$start, $end]))
                ->selectRaw('SUM(unit_price * quantity) as revenue')
                ->value('revenue') ?? 0;
            $discounts = SaleItem::query()
                ->when(!LocationAccess::hasGlobalAccess(), fn ($query) => $query->where('location_id', LocationAccess::assignedLocationId()))
                ->whereHas('sale', fn ($q) => $q->whereBetween('sold_at', [$start, $end]))
                ->sum('discount_amount');
            $expensesTotal = Expense::whereBetween('spent_at', [$start, $end])->sum('amount');

            $profit = $salesRevenue - $cogsTotal - $expensesTotal - $discounts;
            $stockTotal = LocationAccess::filterByLocation(StockBalance::query(), 'location_id')->sum('quantity');
            $stockValue = LocationAccess::filterByLocation(StockBalance::with('product'), 'location_id')->get()->sum(fn ($balance) => (float) $balance->quantity * (float) ($balance->product?->avg_cost_local ?? 0));
            $creditRemaining = LocationAccess::filterSales(Sale::query()->where('type', 'credit'))
                ->whereBetween('sold_at', [$start, $end])
                ->selectRaw('SUM(total_amount - paid_total) as remaining')
                ->value('remaining') ?? 0;

            return [
                'sales' => $salesTotal,
                'revenue' => $salesRevenue,
                'cogs' => $cogsTotal,
                'expenses' => $expensesTotal,
                'profit' => $profit,
                'stock_total' => $stockTotal,
                'stock_value' => $stockValue,
                'credit_remaining' => $creditRemaining,
            ];
        });

        $lowStock = LocationAccess::filterByLocation(StockBalance::with('product'), 'location_id')
            ->whereHas('product', fn ($q) => $q->where('reorder_level', '>', 0))
            ->whereRaw('quantity <= (select reorder_level from products where products.id = stock_balances.product_id)')
            ->orderBy('quantity')
            ->limit(6)
            ->get();

        $negativeStock = LocationAccess::filterByLocation(StockBalance::with('product'), 'location_id')
            ->where('quantity', '<=', 0)
            ->orderBy('quantity')
            ->limit(6)
            ->get();

        $recentPurchases = LocationAccess::filterPurchases(PurchaseOrder::with('supplier')->orderByDesc('id'))->limit(5)->get();
        $recentSales = LocationAccess::filterSales(Sale::with('customer')->orderByDesc('sold_at'))->limit(5)->get();
        $recentActivity = LocationAccess::hasGlobalAccess()
            ? ActivityLog::with('user')->orderByDesc('id')->limit(6)->get()
            : collect();

        return view('livewire.dashboard', compact('kpis', 'lowStock', 'negativeStock', 'recentPurchases', 'recentSales', 'recentActivity'))
            ->layout('layouts.app');
    }
}
