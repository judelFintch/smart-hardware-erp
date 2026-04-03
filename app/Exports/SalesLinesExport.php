<?php

namespace App\Exports;

use App\Models\SaleItem;
use App\Models\StockBalance;
use App\Support\LocationAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesLinesExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly ?string $start = null,
        private readonly ?string $end = null,
        private readonly ?int $locationId = null,
    ) {
    }

    public function headings(): array
    {
        return [
            'Article',
            'SKU',
            'Quantite vendue',
            'Prix vente unitaire moyen',
            'Prix achat unitaire moyen',
            'Total vente',
            'Total achat',
            'Benefice',
            'Quantite restante',
            'Valorisation stock',
        ];
    }

    public function collection(): Collection
    {
        $stockByProduct = $this->stockByProduct();

        return $this->query()
            ->with('product')
            ->orderByDesc('quantity_sold')
            ->orderBy('product_id')
            ->get()
            ->map(function (SaleItem $item) use ($stockByProduct) {
                $quantitySold = (float) ($item->quantity_sold ?? 0);
                $salesTotal = (float) ($item->sales_total ?? 0);
                $purchaseTotal = (float) ($item->purchase_total ?? 0);
                $stock = $stockByProduct->get($item->product_id, ['quantity' => 0.0, 'valuation' => 0.0]);

                return [
                    $item->product?->name ?? '-',
                    $item->product?->sku ?? '-',
                    $this->formatQuantity($quantitySold),
                    $quantitySold > 0 ? $salesTotal / $quantitySold : 0,
                    $quantitySold > 0 ? $purchaseTotal / $quantitySold : 0,
                    $salesTotal,
                    $purchaseTotal,
                    $salesTotal - $purchaseTotal,
                    $this->formatQuantity((float) ($stock['quantity'] ?? 0)),
                    (float) ($stock['valuation'] ?? 0),
                ];
            });
    }

    private function formatQuantity(float|int|string|null $quantity): string
    {
        $value = (float) ($quantity ?? 0);

        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }

    private function query(): Builder
    {
        $query = SaleItem::query();

        if ($this->locationId) {
            $query->where('location_id', $this->locationId);
        } else {
            $query = LocationAccess::filterByLocation($query, 'location_id');
        }

        if ($this->start) {
            $query->whereHas('sale', fn (Builder $builder) => $builder->whereDate('sold_at', '>=', $this->start));
        }

        if ($this->end) {
            $query->whereHas('sale', fn (Builder $builder) => $builder->whereDate('sold_at', '<=', $this->end));
        }

        return $query
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
        $query = StockBalance::query();

        if ($this->locationId) {
            $query->where('location_id', $this->locationId);
        } else {
            $query = LocationAccess::filterByLocation($query, 'location_id');
        }

        return $query
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
}
