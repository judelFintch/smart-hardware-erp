<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return ['ID', 'Fournisseur', 'Type', 'Statut', 'Total', 'Commande', 'Réception'];
    }

    public function collection(): Collection
    {
        return PurchaseOrder::with('supplier')
            ->orderByDesc('id')
            ->get()
            ->map(function (PurchaseOrder $purchase) {
                return [
                    $purchase->id,
                    $purchase->supplier?->name,
                    $purchase->type,
                    $purchase->status,
                    $purchase->total_cost_local,
                    $purchase->ordered_at,
                    $purchase->received_at,
                ];
            });
    }
}
