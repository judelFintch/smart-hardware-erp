<?php

namespace App\Livewire\Reports;

use App\Exports\SalesLinesExport;
use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Support\LocationAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Sales extends Component
{
    use WithPagination;

    public ?string $start = null;
    public ?string $end = null;
    public int $perPage = 25;

    public function updatingStart(): void
    {
        $this->resetPage();
    }

    public function updatingEnd(): void
    {
        $this->resetPage();
    }

    public function applyFilter(): void
    {
        $this->resetPage();
    }

    public function formatQuantity(float|int|string|null $quantity): string
    {
        $value = (float) ($quantity ?? 0);

        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }

    public function exportExcel()
    {
        return Excel::download(
            new SalesLinesExport($this->start, $this->end, LocationAccess::assignedLocationId()),
            'rapport-ventes-lignes.xlsx'
        );
    }

    public function render()
    {
        $query = $this->aggregatedSalesQuery();
        $baseQuery = $this->saleItemsQuery();

        $totals = [
            'products' => (clone $query)->count(),
            'quantity' => (float) ((clone $baseQuery)->sum('quantity') ?? 0),
            'sales' => (float) ((clone $baseQuery)->selectRaw('SUM(unit_price * quantity) as total')->value('total') ?? 0),
            'purchase' => (float) ((clone $baseQuery)->selectRaw('SUM(unit_cost_local * quantity) as total')->value('total') ?? 0),
        ];
        $totals['profit'] = $totals['sales'] - $totals['purchase'];

        $stockByProduct = $this->stockByProduct();

        $saleLines = $query
            ->with('product')
            ->orderByDesc('quantity_sold')
            ->orderBy('product_id')
            ->paginate($this->perPage);

        $saleLines->getCollection()->transform(function (SaleItem $line) use ($stockByProduct) {
            $quantitySold = (float) ($line->quantity_sold ?? 0);
            $salesTotal = (float) ($line->sales_total ?? 0);
            $purchaseTotal = (float) ($line->purchase_total ?? 0);
            $stock = $stockByProduct->get($line->product_id, ['quantity' => 0.0, 'valuation' => 0.0]);

            $line->quantity_sold = $quantitySold;
            $line->sales_total = $salesTotal;
            $line->purchase_total = $purchaseTotal;
            $line->profit_total = $salesTotal - $purchaseTotal;
            $line->unit_sale_price_avg = $quantitySold > 0 ? $salesTotal / $quantitySold : 0;
            $line->unit_purchase_price_avg = $quantitySold > 0 ? $purchaseTotal / $quantitySold : 0;
            $line->remaining_quantity = (float) ($stock['quantity'] ?? 0);
            $line->remaining_valuation = (float) ($stock['valuation'] ?? 0);

            return $line;
        });

        return view('livewire.reports.sales', compact('saleLines', 'totals'))
            ->layout('layouts.app');
    }

    private function saleItemsQuery(): Builder
    {
        $query = SaleItem::query();

        if (!LocationAccess::hasGlobalAccess()) {
            $query->where('location_id', LocationAccess::assignedLocationId());
        }

        if ($this->start) {
            $query->whereHas('sale', fn (Builder $builder) => $builder->whereDate('sold_at', '>=', $this->start));
        }

        if ($this->end) {
            $query->whereHas('sale', fn (Builder $builder) => $builder->whereDate('sold_at', '<=', $this->end));
        }

        return $query;
    }

    private function aggregatedSalesQuery(): Builder
    {
        return $this->saleItemsQuery()
            ->select([
                'product_id',
                DB::raw('SUM(quantity) as quantity_sold'),
                DB::raw('SUM(unit_price * quantity) as sales_total'),
                DB::raw('SUM(unit_cost_local * quantity) as purchase_total'),
            ])
            ->whereNotNull('product_id')
            ->groupBy('product_id');
    }

    private function stockByProduct(): Collection
    {
        return $this->stockBalancesQuery()
            ->select([
                'product_id',
                DB::raw('SUM(quantity) as remaining_quantity'),
                DB::raw('SUM(quantity * avg_cost_local) as remaining_valuation'),
            ])
            ->groupBy('product_id')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->product_id => [
                    'quantity' => (float) ($row->remaining_quantity ?? 0),
                    'valuation' => (float) ($row->remaining_valuation ?? 0),
                ],
            ]);
    }

    private function stockBalancesQuery(): Builder
    {
        $query = StockBalance::query();

        if (!LocationAccess::hasGlobalAccess()) {
            $query->where('location_id', LocationAccess::assignedLocationId());
        }

        return $query;
    }
}
