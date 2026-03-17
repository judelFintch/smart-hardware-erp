<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\Supplier;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public function render()
    {
        $query = trim($this->query);
        $results = [
            'products' => collect(),
            'customers' => collect(),
            'suppliers' => collect(),
            'sales' => collect(),
            'purchases' => collect(),
        ];

        if (mb_strlen($query) >= 2) {
            $results['products'] = Product::query()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('sku', 'like', "%{$query}%")
                ->orWhere('barcode', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit(5)
                ->get();

            $results['customers'] = Customer::query()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit(5)
                ->get();

            $results['suppliers'] = Supplier::query()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit(5)
                ->get();

            $results['sales'] = Sale::query()
                ->with('customer')
                ->where('id', $query)
                ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$query}%"))
                ->orderByDesc('sold_at')
                ->limit(5)
                ->get();

            $results['purchases'] = PurchaseOrder::query()
                ->with('supplier')
                ->where('reference', 'like', "%{$query}%")
                ->orWhereHas('supplier', fn ($q) => $q->where('name', 'like', "%{$query}%"))
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        return view('livewire.global-search', compact('results'));
    }
}
