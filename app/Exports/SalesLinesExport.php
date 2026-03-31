<?php

namespace App\Exports;

use App\Models\SaleItem;
use App\Support\LocationAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
            'Vente',
            'Date',
            'Client',
            'Magasin',
            'Article',
            'Quantite',
            'Prix vente unitaire',
            'Prix achat unitaire',
            'Total vente',
            'Total achat',
            'Benefice',
        ];
    }

    public function collection(): Collection
    {
        return $this->query()
            ->with(['sale.customer', 'product', 'location'])
            ->orderByDesc('sale_id')
            ->orderByDesc('id')
            ->get()
            ->map(function (SaleItem $item) {
                $salesTotal = (float) $item->unit_price * (float) $item->quantity;
                $purchaseTotal = (float) $item->unit_cost_local * (float) $item->quantity;

                return [
                    $item->sale_id,
                    optional($item->sale?->sold_at)->format('Y-m-d H:i'),
                    $item->sale?->customer?->name ?? 'Client comptoir',
                    $item->location?->name ?? '-',
                    $item->product?->name ?? '-',
                    (float) $item->quantity,
                    (float) $item->unit_price,
                    (float) $item->unit_cost_local,
                    $salesTotal,
                    $purchaseTotal,
                    $salesTotal - $purchaseTotal,
                ];
            });
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

        return $query;
    }
}
