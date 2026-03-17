<div>
    <h1 class="text-2xl font-semibold mb-4">Achat #{{ $purchaseOrder->id }}</h1>

    <div class="bg-white shadow rounded p-4 mb-4">
        <p><strong>Fournisseur:</strong> {{ $purchaseOrder->supplier->name }}</p>
        <p><strong>Type:</strong> {{ $purchaseOrder->type }}</p>
        <p><strong>Statut:</strong> {{ $purchaseOrder->status }}</p>
        <p><strong>Total:</strong> {{ number_format($purchaseOrder->total_cost_local, 2) }}</p>
    </div>

    <h2 class="text-lg font-semibold mb-2">Articles</h2>
    <form wire:submit.prevent="receive">
        <table class="w-full bg-white shadow rounded mb-6">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-2">Article</th>
                    <th class="p-2">Quantité commandée</th>
                    <th class="p-2">Quantité réceptionnée</th>
                    <th class="p-2">Coût unitaire</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseOrder->items as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item->product->name }}</td>
                        <td class="p-2">{{ $item->quantity }}</td>
                        <td class="p-2">
                            <input
                                wire:model.defer="receivedQuantities.{{ $item->id }}"
                                type="number"
                                min="0"
                                step="0.001"
                                class="input"
                                value="{{ $item->received_quantity ?? $item->quantity }}"
                            >
                        </td>
                        <td class="p-2">{{ number_format($item->unit_cost_local, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($purchaseOrder->status !== 'approvisionnee')
            <button class="px-3 py-2 bg-green-600 text-white rounded" type="submit">Marquer réception & stocker</button>
        @endif
    </form>

    @if ($purchaseOrder->type === 'foreign')
        <h2 class="text-lg font-semibold mb-2">Transferts</h2>
        <form wire:submit.prevent="addTransfer" class="space-y-2 mb-4">
            <div class="grid grid-cols-2 gap-2">
                <input wire:model.defer="amount_foreign" type="number" step="0.01" class="input" placeholder="Montant devise">
                <input wire:model.defer="amount_local" type="number" step="0.01" class="input" placeholder="Montant local">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input wire:model.defer="paid_at" type="date" class="input">
                <input wire:model.defer="reference" class="input" placeholder="Référence">
            </div>
            <textarea wire:model.defer="notes" class="input" placeholder="Notes"></textarea>
            <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Ajouter transfert</button>
        </form>
    @endif

    @if ($purchaseOrder->status !== 'approvisionnee')
        <button wire:click="receive" class="px-3 py-2 bg-green-600 text-white rounded" type="button">Marquer réception & stocker</button>
    @endif

    <h2 class="text-lg font-semibold mt-6 mb-2">Pièces jointes</h2>
    <form wire:submit.prevent="uploadAttachment" class="space-y-2 mb-4">
        <input type="file" wire:model="attachment" class="input">
        @error('attachment') <span class="text-red-600">{{ $message }}</span> @enderror
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Ajouter pièce</button>
    </form>

    <ul class="list-disc pl-6">
        @foreach ($purchaseOrder->attachments as $file)
            <li class="mb-1">
                <button wire:click="downloadAttachment({{ $file->id }})" class="text-blue-600" type="button">
                    {{ $file->original_name }}
                </button>
            </li>
        @endforeach
    </ul>
</div>
