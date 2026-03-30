<?php

namespace App\Exports;

use App\Models\InventoryCountItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly ?int $locationId = null)
    {
    }

    public function headings(): array
    {
        return ['Inventaire', 'Date', 'Magasin', 'Article', 'Système', 'Compté', 'Différence', 'Valeur'];
    }

    public function collection(): Collection
    {
        return InventoryCountItem::with(['inventoryCount.location', 'product'])
            ->when($this->locationId, fn ($query, $locationId) => $query->whereHas('inventoryCount', fn ($builder) => $builder->where('location_id', $locationId)))
            ->orderByDesc('id')
            ->get()
            ->map(function (InventoryCountItem $item) {
                return [
                    $item->inventory_count_id,
                    $item->inventoryCount?->counted_at,
                    $item->inventoryCount?->location?->name,
                    $item->product?->name,
                    $item->system_quantity,
                    $item->counted_quantity,
                    $item->difference,
                    (float) $item->difference * (float) $item->unit_cost_local,
                ];
            });
    }
}
