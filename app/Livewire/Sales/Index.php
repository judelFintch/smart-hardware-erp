<?php

namespace App\Livewire\Sales;

use App\Exports\SalesExport;
use App\Models\CompanySetting;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 15;

    public function export()
    {
        return Excel::download(new SalesExport(), 'sales.xlsx');
    }

    public function exportPdf()
    {
        $company = CompanySetting::first();
        $sales = Sale::with('customer')->orderByDesc('sold_at')->get();

        $pdf = Pdf::loadView('exports.sales', compact('company', 'sales'));

        return response()->streamDownload(fn () => print($pdf->output()), 'sales.pdf');
    }

    public function render()
    {
        $sales = Sale::with('customer')->orderByDesc('sold_at')->paginate($this->perPage);

        return view('livewire.sales.index', compact('sales'))
            ->layout('layouts.app');
    }
}
