<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Facture Vente #{{ $sale->id }}</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 10px; color: #0f172a; }
            .ticket { width: 80mm; max-width: 80mm; }
            h1 { font-size: 14px; margin: 0 0 4px; text-align: center; }
            .muted { color: #64748b; font-size: 11px; text-align: center; }
            .meta { margin: 8px 0; }
            .row { display: flex; justify-content: space-between; }
            table { width: 100%; border-collapse: collapse; margin-top: 6px; }
            th, td { padding: 4px 0; border-bottom: 1px dashed #e2e8f0; text-align: left; }
            th:last-child, td:last-child { text-align: right; }
            .totals { margin-top: 8px; }
            .totals .row { margin: 2px 0; }
            .footer { margin-top: 8px; text-align: center; font-size: 11px; color: #64748b; }
            .no-print { margin-top: 10px; }
            @media print {
                body { padding: 0; }
                .no-print { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="ticket">
            <h1>{{ $company?->name ?? 'Facture' }}</h1>
            @if ($company?->address)
                <div class="muted">{{ $company->address }}</div>
            @endif
            @if ($company?->phone)
                <div class="muted">{{ $company->phone }}</div>
            @endif

            <div class="meta">
                <div class="row"><span>Facture</span><span>#{{ $sale->id }}</span></div>
                <div class="row"><span>Date</span><span>{{ optional($sale->sold_at)->format('d/m/Y H:i') }}</span></div>
                <div class="row"><span>Client</span><span>{{ $sale->customer?->name ?? 'Comptant' }}</span></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Qté</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->product?->name }}</td>
                            <td>{{ number_format($item->quantity, 3) }}</td>
                            <td>{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div class="row"><span>Sous-total</span><span>{{ number_format($sale->subtotal, 2) }}</span></div>
                <div class="row"><span>Remise globale</span><span>{{ number_format($sale->discount_total, 2) }}</span></div>
                <div class="row"><strong>Total</strong><strong>{{ number_format($sale->total_amount, 2) }}</strong></div>
                <div class="row"><span>Payé</span><span>{{ number_format($sale->paid_total, 2) }}</span></div>
                <div class="row"><span>Statut</span><span>{{ $sale->status }}</span></div>
            </div>

            @if ($company?->invoice_footer)
                <div class="footer">{{ $company->invoice_footer }}</div>
            @endif

            <div class="no-print">
                <button onclick="window.print()">Imprimer</button>
            </div>
        </div>
    </body>
</html>
