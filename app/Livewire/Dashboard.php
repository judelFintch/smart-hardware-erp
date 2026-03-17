<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
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

        $kpis = Cache::remember("dashboard:kpis:{$this->period}", now()->addMinutes(1), function () use ($start, $end) {
            $salesTotal = Sale::whereBetween('sold_at', [$start, $end])->sum('total_amount');
            $cogsTotal = SaleItem::whereHas('sale', fn ($q) => $q->whereBetween('sold_at', [$start, $end]))
                ->selectRaw('SUM(quantity * unit_cost_local) as cogs')
                ->value('cogs') ?? 0;
            $salesRevenue = SaleItem::whereHas('sale', fn ($q) => $q->whereBetween('sold_at', [$start, $end]))
                ->selectRaw('SUM(unit_price * quantity) as revenue')
                ->value('revenue') ?? 0;
            $discounts = SaleItem::whereHas('sale', fn ($q) => $q->whereBetween('sold_at', [$start, $end]))
                ->sum('discount_amount');
            $expensesTotal = Expense::whereBetween('spent_at', [$start, $end])->sum('amount');

            $profit = $salesRevenue - $cogsTotal - $expensesTotal - $discounts;
            $stockTotal = StockBalance::sum('quantity');
            $stockValue = StockBalance::with('product')->get()->sum(fn ($balance) => (float) $balance->quantity * (float) ($balance->product?->avg_cost_local ?? 0));
            $creditRemaining = Sale::where('type', 'credit')
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

        $lowStock = StockBalance::with('product')
            ->whereHas('product', fn ($q) => $q->where('reorder_level', '>', 0))
            ->whereRaw('quantity <= (select reorder_level from products where products.id = stock_balances.product_id)')
            ->orderBy('quantity')
            ->limit(6)
            ->get();

        $negativeStock = StockBalance::with('product')
            ->where('quantity', '<=', 0)
            ->orderBy('quantity')
            ->limit(6)
            ->get();

        $recentPurchases = PurchaseOrder::with('supplier')->orderByDesc('id')->limit(5)->get();
        $recentSales = Sale::with('customer')->orderByDesc('sold_at')->limit(5)->get();
        $recentActivity = ActivityLog::with('user')->orderByDesc('id')->limit(6)->get();

        return view('livewire.dashboard', compact('kpis', 'lowStock', 'negativeStock', 'recentPurchases', 'recentSales', 'recentActivity'))
            ->layout('layouts.app');
    }
}
