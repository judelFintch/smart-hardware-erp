<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function financial(Request $request): View
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $salesQuery = Sale::query();
        $expensesQuery = Expense::query();
        $saleItemsQuery = SaleItem::query();

        if ($start) {
            $salesQuery->whereDate('sold_at', '>=', $start);
            $expensesQuery->whereDate('spent_at', '>=', $start);
            $saleItemsQuery->whereHas('sale', function ($query) use ($start) {
                $query->whereDate('sold_at', '>=', $start);
            });
        }

        if ($end) {
            $salesQuery->whereDate('sold_at', '<=', $end);
            $expensesQuery->whereDate('spent_at', '<=', $end);
            $saleItemsQuery->whereHas('sale', function ($query) use ($end) {
                $query->whereDate('sold_at', '<=', $end);
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

        return view('reports.financial', compact(
            'salesTotal',
            'cogsTotal',
            'expensesTotal',
            'profit',
            'creditOutstanding',
            'stockByLocation',
            'start',
            'end'
        ));
    }
}
