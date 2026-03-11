<?php

namespace App\Livewire\Reports;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Models\StockLocation;
use Livewire\Component;

class Financial extends Component
{
    public ?string $start = null;
    public ?string $end = null;

    public function applyFilter(): void
    {
        // Livewire will re-render with current filters.
    }

    public function render()
    {
        $salesQuery = Sale::query();
        $expensesQuery = Expense::query();
        $saleItemsQuery = SaleItem::query();

        if ($this->start) {
            $salesQuery->whereDate('sold_at', '>=', $this->start);
            $expensesQuery->whereDate('spent_at', '>=', $this->start);
            $saleItemsQuery->whereHas('sale', function ($query) {
                $query->whereDate('sold_at', '>=', $this->start);
            });
        }

        if ($this->end) {
            $salesQuery->whereDate('sold_at', '<=', $this->end);
            $expensesQuery->whereDate('spent_at', '<=', $this->end);
            $saleItemsQuery->whereHas('sale', function ($query) {
                $query->whereDate('sold_at', '<=', $this->end);
            });
        }

        $salesTotal = (float) $salesQuery->sum('total_amount');
        $cogsTotal = (float) $saleItemsQuery
            ->selectRaw('SUM(unit_cost_local * quantity) as total')
            ->value('total');
        $expensesTotal = (float) $expensesQuery->sum('amount');
        $profit = $salesTotal - $cogsTotal - $expensesTotal;

        $creditOutstanding = (float) Sale::where('type', 'credit')
            ->where('status', 'open')
            ->selectRaw('SUM(total_amount - paid_total) as total')
            ->value('total');

        $locations = StockLocation::orderBy('name')->get();
        $stockByLocation = $locations->map(function ($location) {
            $balances = StockBalance::with('product')
                ->where('location_id', $location->id)
                ->get();

            return [
                'location' => $location,
                'balances' => $balances,
            ];
        });

        return view('livewire.reports.financial', compact(
            'salesTotal',
            'cogsTotal',
            'expensesTotal',
            'profit',
            'creditOutstanding',
            'stockByLocation'
        ))->layout('layouts.app');
    }
}
