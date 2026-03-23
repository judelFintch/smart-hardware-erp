<?php

namespace App\Livewire\Products;

use App\Exports\ProductImportTemplateExport;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $importFile;
    public string $search = '';
    public ?int $location_id = null;
    public int $perPage = 15;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingLocationId(): void
    {
        $this->resetPage();
    }

    public function delete(int $productId): void
    {
        Product::whereKey($productId)->delete();
    }

    public function downloadImportTemplate()
    {
        return Excel::download(new ProductImportTemplateExport(), 'modele-import-produits.xlsx');
    }

    public function importCsv(): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:5120'],
        ]);

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
            $product->barcode = $data['barcode'] ?? $product->barcode;
            if (!empty($data['unit_code'])) {
                $unit = Unit::where('code', $data['unit_code'])->first();
                $product->unit_id = $unit?->id ?? $product->unit_id;
            }
            if (!$product->unit_id) {
                $pcs = Unit::where('code', 'pcs')->first();
                $product->unit_id = $pcs?->id;
            }
            $product->description = $data['description'] ?? $product->description;
            $product->sale_margin_percent = isset($data['margin']) ? (float) $data['margin'] : $product->sale_margin_percent;
            $product->reorder_level = isset($data['reorder_level']) ? (float) $data['reorder_level'] : $product->reorder_level;
            if (!$product->exists) {
                $product->avg_cost_local = 0;
                $product->sale_price_local = 0;
            }
            $product->save();
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
            ->orderBy('name')
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

        $locations = StockLocation::orderBy('name')->get();
        $selectedLocation = $locations->firstWhere('id', $selectedLocationId);

        return view('livewire.products.index', compact('products', 'stats', 'locations', 'selectedLocation'))
            ->layout('layouts.app');
    }
}
