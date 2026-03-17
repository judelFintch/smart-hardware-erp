<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $importFile;
    public int $perPage = 15;

    public function delete(int $productId): void
    {
        Product::whereKey($productId)->delete();
    }

    public function importCsv(): void
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

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($columns, $row);
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

        fclose($handle);
        $this->reset('importFile');
    }

    public function render()
    {
        $products = Product::orderBy('name')->paginate($this->perPage);

        return view('livewire.products.index', compact('products'))
            ->layout('layouts.app');
    }
}
