<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly ?int $locationId = null)
    {
    }

    public function headings(): array
    {
        return ['ID', 'Client', 'Type', 'Statut', 'Total', 'Payé', 'Date'];
    }

    public function collection(): Collection
    {
        return Sale::with('customer')
            ->when($this->locationId, fn ($query, $locationId) => $query->whereHas('items', fn ($builder) => $builder->where('location_id', $locationId)))
            ->orderByDesc('sold_at')
            ->get()
            ->map(function (Sale $sale) {
                return [
                    $sale->id,
                    $sale->customer?->name,
                    $sale->type,
                    $sale->status,
                    $sale->total_amount,
                    $sale->paid_total,
                    $sale->sold_at,
                ];
            });
    }
}
