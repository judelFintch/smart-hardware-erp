<?php

namespace App\Livewire\Purchases;

use App\Exports\PurchasesExport;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Support\LocationAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public int $perPage = 15;
    public $importFile;

    public function export()
    {
        return Excel::download(new PurchasesExport(LocationAccess::assignedLocationId()), 'purchases.xlsx');
    }

    public function importCsv(): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $receiveLocationId = LocationAccess::assignedLocationId();

        $path = $this->importFile->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            $this->addError('importFile', 'Impossible de lire le fichier.');
            return;
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $this->addError('importFile', 'Fichier CSV vide.');
            return;
        }

        $columns = array_map('strtolower', $header);
        $grouped = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($columns, $row);
            if (!$data) {
                continue;
            }

            if (empty($data['supplier']) || empty($data['product_sku']) || empty($data['quantity']) || empty($data['unit_price'])) {
                continue;
            }

            $supplier = Supplier::where('name', 'like', '%' . trim($data['supplier']) . '%')->first();
            $product = Product::where('sku', trim($data['product_sku']))->orWhere('name', 'like', '%' . trim($data['product_sku']) . '%')->first();
            if (!$supplier || !$product) {
                continue;
            }

            $reference = trim($data['reference'] ?? 'import');
            $key = $supplier->id . '|' . $reference;
            $grouped[$key]['supplier_id'] = $supplier->id;
            $grouped[$key]['reference'] = $reference;
            $grouped[$key]['type'] = 'local';
            $grouped[$key]['status'] = 'commande';
            $grouped[$key]['ordered_at'] = now();
            $grouped[$key]['items'][] = [
                'product_id' => $product->id,
                'quantity' => (float) $data['quantity'],
                'unit_price' => (float) $data['unit_price'],
                'received_quantity' => isset($data['received_quantity']) ? (float) $data['received_quantity'] : (float) $data['quantity'],
            ];
        }

        foreach ($grouped as $group) {
            $purchase = PurchaseOrder::create([
                'supplier_id' => $group['supplier_id'],
                'type' => $group['type'],
                'status' => $group['status'],
                'reference' => $group['reference'],
                'ordered_at' => now(),
                'currency' => 'CDF',
                'exchange_rate' => 1,
                'subtotal_foreign' => 0,
                'subtotal_local' => 0,
                'accessory_fees_local' => 0,
                'transport_fees_local' => 0,
                'total_cost_local' => 0,
                'receive_location_id' => $receiveLocationId,
            ]);

            $subtotal = 0;
            $qty = 0;

            foreach ($group['items'] as $item) {
                $lineLocal = $item['quantity'] * $item['unit_price'];
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'received_quantity' => $item['received_quantity'],
                    'unit_price_foreign' => 0,
                    'unit_price_local' => $item['unit_price'],
                    'line_total_foreign' => 0,
                    'line_total_local' => $lineLocal,
                    'unit_cost_local' => $item['unit_price'],
                ]);

                $subtotal += $lineLocal;
                $qty += $item['quantity'];
            }

            $purchase->update([
                'subtotal_foreign' => 0,
                'subtotal_local' => $subtotal,
                'total_cost_local' => $subtotal,
            ]);
        }

        fclose($handle);
        $this->reset('importFile');
    }

    public function exportPdf()
    {
        $company = CompanySetting::first();
        $purchases = LocationAccess::filterPurchases(PurchaseOrder::with('supplier')->orderByDesc('id'))->get();

        $pdf = Pdf::loadView('exports.purchases', compact('company', 'purchases'));

        return response()->streamDownload(fn () => print($pdf->output()), 'purchases.pdf');
    }

    public function render()
    {
        $query = LocationAccess::filterPurchases(PurchaseOrder::with(['supplier', 'receiveLocation'])->orderByDesc('id'));
        $purchases = (clone $query)->paginate($this->perPage);

        $stats = [
            'count' => (clone $query)->count(),
            'total_cost' => (float) ((clone $query)->sum('total_cost_local') ?? 0),
            'in_progress' => (clone $query)->whereNotIn('status', ['approvisionnee'])->count(),
            'received' => (clone $query)->where('status', 'approvisionnee')->count(),
        ];

        return view('livewire.purchases.index', compact('purchases', 'stats'))
            ->layout('layouts.app');
    }
}
