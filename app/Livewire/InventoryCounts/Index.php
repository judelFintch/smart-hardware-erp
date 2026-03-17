<?php

namespace App\Livewire\InventoryCounts;

use App\Exports\InventoryExport;
use App\Models\CompanySetting;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\StockLocation;
use App\Services\StockService;
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
        return Excel::download(new InventoryExport(), 'inventaires.xlsx');
    }

    public function importCsv(StockService $stockService): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);
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
        $locationId = StockLocation::where('code', 'depot')->first()?->id;
        if (!$locationId) {
            fclose($handle);
            $this->addError('importFile', 'Aucun dépôt trouvé pour importer l’inventaire.');
            return;
        }

        $inventory = InventoryCount::create([
            'location_id' => $locationId,
            'counted_at' => now(),
            'notes' => 'Import CSV',
        ]);

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($columns, $row);
            if (!$data || empty($data['sku']) || !isset($data['quantity'])) {
                continue;
            }
            $product = Product::where('sku', trim($data['sku']))->first();
            if (!$product) {
                continue;
            }

            $countedQty = (float) $data['quantity'];
            $balance = $product->stockBalances()->where('location_id', $locationId)->first();
            $systemQty = (float) ($balance?->quantity ?? 0);
            $diff = $countedQty - $systemQty;
            $unitCost = (float) ($data['unit_cost_local'] ?? $balance?->avg_cost_local ?? $product->avg_cost_local);
            $unitSale = (float) ($data['unit_sale_price_local'] ?? $product->sale_price_local);

            InventoryCountItem::create([
                'inventory_count_id' => $inventory->id,
                'product_id' => $product->id,
                'counted_quantity' => $countedQty,
                'system_quantity' => $systemQty,
                'difference' => $diff,
                'unit_cost_local' => $unitCost,
                'unit_sale_price_local' => $unitSale,
            ]);

            if ($diff !== 0.0) {
                $stockService->recordMovement([
                    'product_id' => $product->id,
                    'from_location_id' => $diff < 0 ? $locationId : null,
                    'to_location_id' => $diff > 0 ? $locationId : null,
                    'quantity' => abs($diff),
                    'unit_cost_local' => $unitCost,
                    'unit_sale_price_local' => $unitSale,
                    'type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                    'reference_type' => InventoryCount::class,
                    'reference_id' => $inventory->id,
                    'occurred_at' => $inventory->counted_at ?? now(),
                ]);
            }
        }

        fclose($handle);
        $this->reset('importFile');
    }

    public function exportPdf()
    {
        $company = CompanySetting::first();
        $items = InventoryCountItem::with(['inventoryCount.location', 'product'])->orderByDesc('id')->get();
        $pdf = Pdf::loadView('exports.inventory', compact('company', 'items'));

        return response()->streamDownload(fn () => print($pdf->output()), 'inventaires.pdf');
    }

    public function render()
    {
        $latestCount = InventoryCount::with('items')
            ->orderByDesc('id')
            ->first();

        $summary = null;
        if ($latestCount) {
            $missingQty = 0.0;
            $missingValue = 0.0;
            $surplusQty = 0.0;
            $surplusValue = 0.0;

            foreach ($latestCount->items as $item) {
                $diff = (float) $item->difference;
                $value = $diff * (float) $item->unit_cost_local;

                if ($diff < 0) {
                    $missingQty += abs($diff);
                    $missingValue += abs($value);
                } elseif ($diff > 0) {
                    $surplusQty += $diff;
                    $surplusValue += $value;
                }
            }

            $summary = [
                'count' => $latestCount,
                'missing_qty' => $missingQty,
                'missing_value' => $missingValue,
                'surplus_qty' => $surplusQty,
                'surplus_value' => $surplusValue,
            ];
        }

        $counts = InventoryCount::with('location')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.inventory-counts.index', compact('counts', 'summary'))
            ->layout('layouts.app');
    }
}
