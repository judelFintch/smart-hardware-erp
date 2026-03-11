<?php

namespace App\Livewire\Sales;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockBalance;
use App\Models\StockLocation;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Show extends Component
{
    public Sale $sale;

    public float $payment_amount = 0;
    public ?string $payment_paid_at = null;
    public string $payment_method = '';
    public string $payment_reference = '';
    public string $payment_notes = '';

    public ?int $return_product_id = null;
    public float $return_quantity = 0;

    public function mount(Sale $sale): void
    {
        $this->sale = $sale->load(['customer', 'items.product', 'payments']);
        $this->return_product_id = $this->sale->items->first()?->product_id;
    }

    public function addPayment(): void
    {
        $data = $this->validate([
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_paid_at' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_notes' => ['nullable', 'string'],
        ]);

        SalePayment::create([
            'sale_id' => $this->sale->id,
            'amount' => (float) $data['payment_amount'],
            'paid_at' => $data['payment_paid_at'] ?? null,
            'method' => $data['payment_method'] ?? null,
            'reference' => $data['payment_reference'] ?? null,
            'notes' => $data['payment_notes'] ?? null,
        ]);

        $paidTotal = (float) $this->sale->payments()->sum('amount');
        $status = $paidTotal >= (float) $this->sale->total_amount ? 'paid' : 'open';

        $this->sale->update([
            'paid_total' => $paidTotal,
            'status' => $status,
        ]);

        $this->reset(['payment_amount', 'payment_paid_at', 'payment_method', 'payment_reference', 'payment_notes']);
        $this->sale->refresh()->load(['customer', 'items.product', 'payments']);
    }

    public function returnItem(StockService $stockService): void
    {
        $data = $this->validate([
            'return_product_id' => ['required', 'exists:products,id'],
            'return_quantity' => ['required', 'numeric', 'min:0.001'],
        ]);

        DB::transaction(function () use ($data, $stockService) {
            $product = Product::findOrFail($data['return_product_id']);
            $quantity = (float) $data['return_quantity'];

            $magasin = StockLocation::where('code', 'magasin')->firstOrFail();
            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $magasin->id)
                ->first();

            $unitCost = $balance?->avg_cost_local ?? $product->avg_cost_local;
            $unitPrice = $product->sale_price_local;

            SaleItem::create([
                'sale_id' => $this->sale->id,
                'product_id' => $product->id,
                'location_id' => $magasin->id,
                'quantity' => -$quantity,
                'unit_price' => $unitPrice,
                'unit_cost_local' => $unitCost,
                'discount_amount' => 0,
                'line_total' => -($unitPrice * $quantity),
            ]);

            $stockService->recordMovement([
                'product_id' => $product->id,
                'from_location_id' => null,
                'to_location_id' => $magasin->id,
                'quantity' => $quantity,
                'unit_cost_local' => $unitCost,
                'unit_sale_price_local' => $unitPrice,
                'type' => 'return_in',
                'reference_type' => Sale::class,
                'reference_id' => $this->sale->id,
                'occurred_at' => now(),
            ]);

            $this->sale->refresh();
            $subtotal = (float) $this->sale->items()->sum(DB::raw('unit_price * quantity'));
            $discountTotal = (float) $this->sale->items()->sum('discount_amount');
            $total = $subtotal - $discountTotal;

            $status = $this->sale->type === 'credit' && $this->sale->paid_total < $total ? 'open' : 'paid';

            $this->sale->update([
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'total_amount' => $total,
                'status' => $status,
            ]);
        });

        $this->reset(['return_quantity']);
        $this->sale->refresh()->load(['customer', 'items.product', 'payments']);
    }

    public function render()
    {
        return view('livewire.sales.show')
            ->layout('layouts.app');
    }
}
