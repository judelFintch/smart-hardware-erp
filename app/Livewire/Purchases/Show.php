<?php

namespace App\Livewire\Purchases;

use App\Models\Attachment;
use App\Models\PurchaseOrder;
use App\Models\PurchaseTransfer;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public PurchaseOrder $purchaseOrder;
    public ?int $receive_location_id = null;

    public float $amount_foreign = 0;
    public float $amount_local = 0;
    public ?string $paid_at = null;
    public string $reference = '';
    public string $notes = '';
    public array $receivedQuantities = [];

    #[Validate('nullable|file|max:5120|mimes:pdf,jpg,jpeg,png')]
    public $attachment;

    public function mount(PurchaseOrder $purchaseOrder): void
    {
        $this->purchaseOrder = $purchaseOrder->load(['supplier', 'items.product', 'transfers', 'attachments', 'receiveLocation']);
        $defaultLocation = StockLocation::where('code', 'depot')->first();
        $this->receive_location_id = $purchaseOrder->receive_location_id ?? $defaultLocation?->id;

        foreach ($this->purchaseOrder->items as $item) {
            $this->receivedQuantities[$item->id] = $item->received_quantity ?? $item->quantity;
        }
    }

    public function addTransfer(): void
    {
        $data = $this->validate([
            'amount_foreign' => ['nullable', 'numeric', 'min:0'],
            'amount_local' => ['nullable', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        PurchaseTransfer::create([
            'purchase_order_id' => $this->purchaseOrder->id,
            'amount_foreign' => (float) ($data['amount_foreign'] ?? 0),
            'amount_local' => (float) ($data['amount_local'] ?? 0),
            'paid_at' => $data['paid_at'] ?? null,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->reset(['amount_foreign', 'amount_local', 'paid_at', 'reference', 'notes']);
        $this->purchaseOrder->refresh()->load(['supplier', 'items.product', 'transfers']);
    }

    public function uploadAttachment(): void
    {
        $this->validateOnly('attachment');

        if (!$this->attachment) {
            return;
        }

        $path = $this->attachment->store('attachments/purchases');

        Attachment::create([
            'file_path' => $path,
            'original_name' => $this->attachment->getClientOriginalName(),
            'mime_type' => $this->attachment->getMimeType(),
            'size' => $this->attachment->getSize(),
            'attachable_type' => PurchaseOrder::class,
            'attachable_id' => $this->purchaseOrder->id,
        ]);

        $this->reset('attachment');
        $this->purchaseOrder->refresh()->load(['attachments']);
    }

    public function downloadAttachment(int $attachmentId)
    {
        $attachment = Attachment::where('attachable_type', PurchaseOrder::class)
            ->where('attachable_id', $this->purchaseOrder->id)
            ->whereKey($attachmentId)
            ->firstOrFail();

        return Storage::download($attachment->file_path, $attachment->original_name);
    }

    public function receive(StockService $stockService): void
    {
        $data = $this->validate([
            'receive_location_id' => ['required', 'exists:stock_locations,id'],
        ]);

        DB::transaction(function () use ($stockService, $data) {
            $destination = StockLocation::findOrFail($data['receive_location_id']);

            if ($this->purchaseOrder->status !== 'approvisionnee') {
                $this->purchaseOrder->update([
                    'status' => 'approvisionnee',
                    'receive_location_id' => $destination->id,
                    'received_at' => $this->purchaseOrder->received_at ?? now()->toDateString(),
                ]);
            } elseif ((int) $this->purchaseOrder->receive_location_id !== $destination->id) {
                $this->purchaseOrder->update([
                    'receive_location_id' => $destination->id,
                ]);
            }

            $alreadyReceived = StockMovement::where('reference_type', PurchaseOrder::class)
                ->where('reference_id', $this->purchaseOrder->id)
                ->where('type', 'purchase_in')
                ->exists();

            if ($alreadyReceived) {
                return;
            }

            $this->purchaseOrder->load('items.product');

            foreach ($this->purchaseOrder->items as $item) {
                $receivedQuantity = isset($this->receivedQuantities[$item->id]) ? (float) $this->receivedQuantities[$item->id] : $item->quantity;
                $receivedQuantity = max(0, $receivedQuantity);
                $item->update(['received_quantity' => $receivedQuantity]);

                if ($receivedQuantity <= 0) {
                    continue;
                }

                $unitCost = (float) $item->unit_cost_local;
                $unitSale = (float) $item->product->sale_price_local;

                $stockService->recordMovement([
                    'product_id' => $item->product_id,
                    'from_location_id' => null,
                    'to_location_id' => $destination->id,
                    'quantity' => $receivedQuantity,
                    'unit_cost_local' => $unitCost,
                    'unit_sale_price_local' => $unitSale,
                    'type' => 'purchase_in',
                    'reference_type' => PurchaseOrder::class,
                    'reference_id' => $this->purchaseOrder->id,
                    'occurred_at' => $this->purchaseOrder->received_at ?? now(),
                ]);
            }
        });

        $this->purchaseOrder->refresh()->load(['supplier', 'items.product', 'transfers', 'attachments', 'receiveLocation']);
    }

    public function render()
    {
        $locations = StockLocation::orderBy('name')->get();

        return view('livewire.purchases.show', compact('locations'))
            ->layout('layouts.app');
    }
}
