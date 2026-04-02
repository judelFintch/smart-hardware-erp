<?php

namespace App\Livewire\InventoryCounts;

use App\Exports\InventoryExport;
use App\Exports\InventoryTemplateExport;
use App\Models\CompanySetting;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\StockLocation;
use App\Services\StockService;
use App\Support\LocationAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public int $perPage = 15;
    public $importFile;
    public ?int $template_location_id = null;
    public ?int $import_location_id = null;
    public ?string $template_counted_at = null;

    public function export()
    {
        return Excel::download(new InventoryExport(LocationAccess::assignedLocationId()), 'inventaires.xlsx');
    }

    public function mount(): void
    {
        $defaultLocationId = LocationAccess::assignedLocationId()
            ?? StockLocation::where('code', 'depot')->first()?->id;

        $this->template_location_id = $defaultLocationId;
        $this->import_location_id = $defaultLocationId;
        $this->template_counted_at = now()->toDateString();
    }

    public function downloadTemplate()
    {
        $this->validate([
            'template_location_id' => ['required', 'exists:stock_locations,id'],
            'template_counted_at' => ['nullable', 'date'],
        ]);

        LocationAccess::ensureLocationAllowed((int) $this->template_location_id);

        $location = StockLocation::findOrFail($this->template_location_id);
        $date = $this->template_counted_at ?: now()->toDateString();

        return Excel::download(
            new InventoryTemplateExport($this->template_location_id, $date),
            sprintf('modele-inventaire-%s-%s.xlsx', $location->code ?: $location->id, $date)
        );
    }

    public function importInventorySheet(StockService $stockService): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:10240'],
            'import_location_id' => ['required', 'exists:stock_locations,id'],
        ]);

        LocationAccess::ensureLocationAllowed((int) $this->import_location_id);

        $rows = $this->extractImportRows();
        if (empty($rows)) {
            $this->addError('importFile', 'Impossible de lire le fichier ou fichier vide.');
            return;
        }

        $header = array_shift($rows);
        $columns = array_map(fn ($value) => strtolower(trim((string) $value)), $header);

        $locationId = (int) $this->import_location_id;
        if (!$locationId) {
            $this->addError('import_location_id', 'Choisissez un emplacement pour l’import.');
            return;
        }

        $preparedRows = [];
        $countedAt = now();

        foreach ($rows as $row) {
            $normalizedRow = array_slice(array_pad($row, count($columns), null), 0, count($columns));
            $data = array_combine($columns, $normalizedRow);
            if (!$data || empty(trim((string) ($data['sku'] ?? ''))) || !isset($data['counted_quantity']) || $data['counted_quantity'] === '') {
                continue;
            }

            $product = Product::where('sku', trim((string) $data['sku']))->first();
            if (!$product) {
                continue;
            }

            $preparedRows[] = [
                'product' => $product,
                'counted_quantity' => (float) $data['counted_quantity'],
                'counted_at' => !empty($data['counted_at']) ? $data['counted_at'] : null,
                'unit_cost_local' => $data['unit_cost_local'] ?? null,
                'unit_sale_price_local' => $data['unit_sale_price_local'] ?? null,
                'system_quantity' => $data['system_quantity'] ?? null,
            ];
        }

        if (count($preparedRows) === 0) {
            $this->addError('importFile', 'Aucune ligne exploitable trouvée. Vérifiez les colonnes sku et counted_quantity.');
            return;
        }

        foreach ($preparedRows as $row) {
            if (!empty($row['counted_at'])) {
                $countedAt = $row['counted_at'];
                break;
            }
        }

        $inventory = InventoryCount::create([
            'location_id' => $locationId,
            'counted_at' => $countedAt,
            'notes' => 'Import inventaire Excel/CSV',
            'created_by' => auth()->id(),
        ]);

        foreach ($preparedRows as $row) {
            $product = $row['product'];
            $countedQty = $row['counted_quantity'];
            $balance = $product->stockBalances()->where('location_id', $locationId)->first();
            $systemQty = isset($row['system_quantity']) && $row['system_quantity'] !== ''
                ? (float) $row['system_quantity']
                : (float) ($balance?->quantity ?? 0);
            $diff = $countedQty - $systemQty;
            $unitCost = (float) ($row['unit_cost_local'] ?? $balance?->avg_cost_local ?? $product->avg_cost_local);
            $unitSale = (float) ($row['unit_sale_price_local'] ?? $product->sale_price_local);

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

        $this->reset('importFile');
    }

    public function exportPdf()
    {
        $company = CompanySetting::first();
        $items = InventoryCountItem::with(['inventoryCount.location', 'product'])
            ->whereHas('inventoryCount', fn ($query) => LocationAccess::filterInventoryCounts($query))
            ->orderByDesc('id')
            ->get();
        $pdf = Pdf::loadView('exports.inventory', compact('company', 'items'));

        return response()->streamDownload(fn () => print($pdf->output()), 'inventaires.pdf');
    }

    public function render()
    {
        $locations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();
        $canSelectAnyLocation = LocationAccess::hasGlobalAccess();
        $latestCount = LocationAccess::filterInventoryCounts(InventoryCount::with('items'))
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

        $counts = LocationAccess::filterInventoryCounts(InventoryCount::with('location'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.inventory-counts.index', compact('counts', 'summary', 'locations', 'canSelectAnyLocation'))
            ->layout('layouts.app');
    }

    protected function extractImportRows(): array
    {
        $path = $this->importFile->getRealPath();
        $extension = strtolower($this->importFile->getClientOriginalExtension());

        if (in_array($extension, ['xls', 'xlsx'], true)) {
            $spreadsheet = IOFactory::load($path);

            return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }
}
