<?php

namespace App\Exports;

use App\Models\StockBalance;
use App\Models\StockLocation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryTemplateExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly int $locationId,
        private readonly ?string $countedAt = null,
    ) {
    }

    public function headings(): array
    {
        return [
            'sku',
            'name',
            'location_code',
            'location_name',
            'system_quantity',
            'counted_quantity',
            'counted_at',
            'unit_cost_local',
            'unit_sale_price_local',
        ];
    }

    public function collection(): Collection
    {
        $location = StockLocation::findOrFail($this->locationId);
        $countedAt = $this->countedAt ?: now()->toDateString();

        return StockBalance::query()
            ->with('product')
            ->where('location_id', $this->locationId)
            ->orderByDesc('quantity')
            ->get()
            ->sortBy(fn (StockBalance $balance) => $balance->product?->name ?? '')
            ->values()
            ->map(function (StockBalance $balance) use ($location, $countedAt) {
                return [
                    $balance->product?->sku,
                    $balance->product?->name,
                    $location->code,
                    $location->name,
                    (float) $balance->quantity,
                    null,
                    $countedAt,
                    (float) ($balance->avg_cost_local ?? $balance->product?->avg_cost_local ?? 0),
                    (float) ($balance->sale_price_local ?? $balance->product?->sale_price_local ?? 0),
                ];
            });
    }
}
