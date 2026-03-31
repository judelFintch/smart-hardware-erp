<?php

namespace App\Livewire\Reports;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Support\LocationAccess;
use Livewire\Component;

class Financial extends Component
{
    public ?string $start = null;
    public ?string $end = null;
    public string $profitCalculationMode = 'applied_sale_price';

    public function applyFilter(): void
    {
        // Livewire will re-render with current filters.
    }

    public function render()
    {
        $salesQuery = LocationAccess::filterSales(Sale::query());
        $expensesQuery = Expense::query();
        $saleItemsQuery = SaleItem::query();
        if (!LocationAccess::hasGlobalAccess()) {
            $saleItemsQuery->where('location_id', LocationAccess::assignedLocationId());
        }

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
        $discountsTotal = (float) $salesQuery->sum('discount_total');
        $cogsTotal = (float) $saleItemsQuery
            ->selectRaw('SUM(unit_cost_local * quantity) as total')
            ->value('total');
        $appliedSalePriceTotal = (float) $saleItemsQuery
            ->selectRaw('SUM(unit_price * quantity) as total')
            ->value('total');
        $expensesTotal = (float) $expensesQuery->sum('amount');
        $profitRevenue = match ($this->profitCalculationMode) {
            'net_sales' => $salesTotal,
            default => $appliedSalePriceTotal,
        };
        $profit = $profitRevenue - $cogsTotal - $expensesTotal;

        $creditOutstanding = (float) LocationAccess::filterSales(Sale::query()->where('type', 'credit'))
            ->where('status', 'open')
            ->selectRaw('SUM(total_amount - paid_total) as total')
            ->value('total');

        $locations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();
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
            'discountsTotal',
            'cogsTotal',
            'appliedSalePriceTotal',
            'profitRevenue',
            'expensesTotal',
            'profit',
            'creditOutstanding',
            'stockByLocation'
        ))->layout('layouts.app');
    }
}
