<?php

namespace App\Livewire\Products;

use App\Exports\ProductImportTemplateExport;
use App\Livewire\Concerns\ConfirmsDeletionWithSecretCode;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Models\Unit;
use App\Services\StockService;
use App\Support\LocationAccess;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Index extends Component
{
    use ConfirmsDeletionWithSecretCode, WithFileUploads, WithPagination;

    public $importFile;
    public string $search = '';
    public ?int $location_id = null;
    public ?int $import_location_id = null;
    public int $perPage = 15;
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    public function mount(): void
    {
        $defaultLocationId = LocationAccess::assignedLocationId()
            ?? StockLocation::where('code', 'depot')->first()?->id;
        $this->location_id = $defaultLocationId;
        $this->import_location_id = $defaultLocationId;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingLocationId(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $allowed = ['name', 'sku', 'filtered_stock_quantity', 'avg_cost_local', 'sale_price_local'];
        if (!in_array($field, $allowed, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    protected function performDelete(int $productId): void
    {
        Product::whereKey($productId)->delete();
    }

    public function downloadImportTemplate()
    {
        return Excel::download(new ProductImportTemplateExport(), 'modele-import-produits.xlsx');
    }

    public function importCsv(StockService $stockService): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:5120'],
            'import_location_id' => ['nullable', 'exists:stock_locations,id'],
        ]);
        if ($this->import_location_id) {
            LocationAccess::ensureLocationAllowed((int) $this->import_location_id);
        }

        $rows = $this->extractImportRows();
        if (empty($rows)) {
            $this->addError('importFile', 'Impossible de lire le fichier ou fichier vide.');
            return;
        }

        $header = array_shift($rows);
        $columns = array_map(fn ($value) => strtolower(trim((string) $value)), $header);

        foreach ($rows as $row) {
            $normalizedRow = array_slice(array_pad($row, count($columns), null), 0, count($columns));
            $data = array_combine($columns, $normalizedRow);
            if (!$data || empty($data['sku']) || empty($data['name'])) {
                continue;
            }

            $product = Product::firstOrNew(['sku' => $data['sku']]);
            $product->name = $data['name'];
            $product->barcode = blank($data['barcode'] ?? null) ? null : trim((string) $data['barcode']);
            if (!empty($data['unit_code'])) {
                $unit = Unit::where('code', $data['unit_code'])->first();
                $product->unit_id = $unit?->id ?? $product->unit_id;
            }
            if (!$product->unit_id) {
                $pcs = Unit::where('code', 'pcs')->first();
                $product->unit_id = $pcs?->id;
            }
            $product->description = $data['description'] ?? $product->description;
            $product->avg_cost_local = isset($data['cost']) && $data['cost'] !== '' ? (float) $data['cost'] : $product->avg_cost_local;
            $product->sale_price_local = isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : $product->sale_price_local;
            $product->sale_margin_percent = isset($data['margin']) ? (float) $data['margin'] : $product->sale_margin_percent;
            $product->reorder_level = isset($data['reorder_level']) ? (float) $data['reorder_level'] : $product->reorder_level;
            if (!$product->exists) {
                $product->avg_cost_local = isset($data['cost']) && $data['cost'] !== '' ? (float) $data['cost'] : 0;
                $product->sale_price_local = isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : 0;
            }
            $product->save();

            $stock = isset($data['stock']) && $data['stock'] !== '' ? (float) $data['stock'] : 0;
            if ($stock > 0 && $this->import_location_id) {
                $stockService->recordMovement([
                    'product_id' => $product->id,
                    'from_location_id' => null,
                    'to_location_id' => $this->import_location_id,
                    'quantity' => $stock,
                    'unit_cost_local' => (float) $product->avg_cost_local,
                    'unit_sale_price_local' => (float) $product->sale_price_local,
                    'type' => 'adjustment_in',
                    'reference_type' => 'product_import',
                    'reference_id' => null,
                    'occurred_at' => now(),
                    'note' => 'Import produits',
                ]);
            }
        }

        $this->reset('importFile');
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

    public function render()
    {
        $selectedLocationId = $this->location_id;
        if (!LocationAccess::hasGlobalAccess()) {
            $selectedLocationId = LocationAccess::assignedLocationId();
            $this->location_id = $selectedLocationId;
            $this->import_location_id = $selectedLocationId;
        }

        $products = Product::query()
            ->with('unit')
            ->withSum([
                'stockBalances as filtered_stock_quantity' => function ($query) use ($selectedLocationId) {
                    if ($selectedLocationId) {
                        $query->where('location_id', $selectedLocationId);
                    }
                },
            ], 'quantity')
            ->when($this->search !== '', function ($query) {
                $like = '%' . trim($this->search) . '%';

                $query->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('sku', 'like', $like)
                        ->orWhere('barcode', 'like', $like);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id')
            ->paginate($this->perPage);

        $stockBalanceQuery = StockBalance::query();
        if ($selectedLocationId) {
            $stockBalanceQuery->where('location_id', $selectedLocationId);
        }

        $stats = [
            'products_count' => Product::count(),
            'stock_total' => (float) $stockBalanceQuery->sum('quantity'),
            'low_stock_count' => Product::query()
                ->withSum([
                    'stockBalances as filtered_stock_quantity' => function ($query) use ($selectedLocationId) {
                        if ($selectedLocationId) {
                            $query->where('location_id', $selectedLocationId);
                        }
                    },
                ], 'quantity')
                ->get()
                ->filter(fn (Product $product) => (float) ($product->filtered_stock_quantity ?? 0) <= (float) $product->reorder_level)
                ->count(),
        ];

        $locations = LocationAccess::restrictLocations(StockLocation::query()->orderBy('name'))->get();
        $selectedLocation = $locations->firstWhere('id', $selectedLocationId);
        $canSelectAnyLocation = LocationAccess::hasGlobalAccess();

        return view('livewire.products.index', compact('products', 'stats', 'locations', 'selectedLocation', 'canSelectAnyLocation'))
            ->layout('layouts.app');
    }
}
