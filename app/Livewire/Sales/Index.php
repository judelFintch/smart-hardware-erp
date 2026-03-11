<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    public function export(): StreamedResponse
    {
        $sales = Sale::with('customer')->orderByDesc('sold_at')->get();

        return response()->streamDownload(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Client', 'Type', 'Statut', 'Total', 'Payé', 'Date']);

            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->id,
                    $sale->customer?->name,
                    $sale->type,
                    $sale->status,
                    $sale->total_amount,
                    $sale->paid_total,
                    $sale->sold_at,
                ]);
            }

            fclose($handle);
        }, 'sales.csv', ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $sales = Sale::with('customer')->orderByDesc('sold_at')->get();

        return view('livewire.sales.index', compact('sales'))
            ->layout('layouts.app');
    }
}
