<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalePaymentController extends Controller
{
    public function store(Request $request, Sale $sale): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['nullable', 'date'],
            'method' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        SalePayment::create([
            'sale_id' => $sale->id,
            'amount' => (float) $data['amount'],
            'paid_at' => $data['paid_at'] ?? null,
            'method' => $data['method'] ?? null,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $paidTotal = (float) $sale->payments()->sum('amount');
        $status = $paidTotal >= (float) $sale->total_amount ? 'paid' : 'open';

        $sale->update([
            'paid_total' => $paidTotal,
            'status' => $status,
        ]);

        return redirect()->route('sales.show', $sale);
    }
}
