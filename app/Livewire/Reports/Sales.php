<?php

namespace App\Livewire\Reports;

use App\Exports\SalesLinesExport;
use App\Models\SaleItem;
use App\Support\LocationAccess;
use Illuminate\Database\Eloquent\Builder;
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

    public function exportExcel()
    {
        return Excel::download(
            new SalesLinesExport($this->start, $this->end, LocationAccess::assignedLocationId()),
            'rapport-ventes-lignes.xlsx'
        );
    }

    public function render()
    {
        $query = $this->saleItemsQuery();

        $totals = [
            'lines' => (clone $query)->count(),
            'quantity' => (float) ((clone $query)->sum('quantity') ?? 0),
            'sales' => (float) ((clone $query)->selectRaw('SUM(unit_price * quantity) as total')->value('total') ?? 0),
            'purchase' => (float) ((clone $query)->selectRaw('SUM(unit_cost_local * quantity) as total')->value('total') ?? 0),
        ];
        $totals['profit'] = $totals['sales'] - $totals['purchase'];

        $saleLines = $query
            ->with(['sale.customer', 'product', 'location'])
            ->orderByDesc('sale_id')
            ->orderByDesc('id')
            ->paginate($this->perPage);

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
}
