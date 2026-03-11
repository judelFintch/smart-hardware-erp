<?php

namespace App\Livewire\Purchases;

use App\Models\PurchaseOrder;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    public function export(): StreamedResponse
    {
        $purchases = PurchaseOrder::with('supplier')->orderByDesc('id')->get();

        return response()->streamDownload(function () use ($purchases) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Fournisseur', 'Type', 'Statut', 'Total', 'Commande', 'Réception']);

            foreach ($purchases as $purchase) {
                fputcsv($handle, [
                    $purchase->id,
                    $purchase->supplier->name,
                    $purchase->type,
                    $purchase->status,
                    $purchase->total_cost_local,
                    $purchase->ordered_at,
                    $purchase->received_at,
                ]);
            }

            fclose($handle);
        }, 'purchases.csv', ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $purchases = PurchaseOrder::with('supplier')->orderByDesc('id')->get();

        return view('livewire.purchases.index', compact('purchases'))
            ->layout('layouts.app');
    }
}
