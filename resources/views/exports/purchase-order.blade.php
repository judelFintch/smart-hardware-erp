<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bon de commande {{ $purchaseOrder->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; margin: 0; padding: 0; }
        .page { padding: 16px; }
        .header { margin-bottom: 18px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header .company { font-size: 14px; color: #334155; margin-top: 3px; }
        .section { margin-bottom: 14px; }
        .section-heading { font-weight: bold; margin-bottom: 6px; }
        .grid { display: table; width: 100%; margin-bottom: 6px; }
        .row { display: table-row; }
        .cell { display: table-cell; vertical-align: top; padding: 2px 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #e2e8f0; padding: 5px; text-align: left; }
        th { background: #f8fafc; }
        .totals { margin-top: 8px; width: 100%; border-collapse: collapse; }
        .totals td { border: none; padding: 3px 5px; }
        .right { text-align: right; }
        .muted { color: #64748b; font-size: 11px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Bon de commande #{{ $purchaseOrder->id }}</h1>
            @if ($company)
                <div class="company">{{ $company->name }} - {{ $company->address }}</div>
            @endif
            <div class="muted">Généré le {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <div class="section">
            <div class="section-heading">Informations de commande</div>
            <div class="grid">
                <div class="row">
                    <div class="cell"><strong>Fournisseur :</strong> {{ $purchaseOrder->supplier?->name ?? 'N/A' }}</div>
                    <div class="cell"><strong>Type :</strong> {{ ucfirst($purchaseOrder->type) }}</div>
                </div>
                <div class="row">
                    <div class="cell"><strong>Statut :</strong> {{ ucfirst($purchaseOrder->status) }}</div>
                    <div class="cell"><strong>Référence :</strong> {{ $purchaseOrder->reference ?? '-' }}</div>
                </div>
                <div class="row">
                    <div class="cell"><strong>Commandé le :</strong> {{ optional($purchaseOrder->ordered_at)->format('d/m/Y') ?? '-' }}</div>
                    <div class="cell"><strong>Reçu le :</strong> {{ optional($purchaseOrder->received_at)->format('d/m/Y') ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-heading">Lignes de commande</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Article</th>
                        <th>Qté</th>
                        <th>Prix unitaire</th>
                        <th>Total ligne</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseOrder->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product?->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price_local, 2) }}</td>
                            <td>{{ number_format($item->line_total_local, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="totals">
                <tr>
                    <td class="right"><strong>Sous-total :</strong></td>
                    <td class="right">{{ number_format($purchaseOrder->subtotal_local, 2) }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>Frais accessoires :</strong></td>
                    <td class="right">{{ number_format($purchaseOrder->accessory_fees_local, 2) }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>Frais transport :</strong></td>
                    <td class="right">{{ number_format($purchaseOrder->transport_fees_local, 2) }}</td>
                </tr>
                <tr>
                    <td class="right"><strong>Total :</strong></td>
                    <td class="right"><strong>{{ number_format($purchaseOrder->total_cost_local, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-heading">Notes</div>
            <div>{{ $purchaseOrder->notes ?? '-' }}</div>
        </div>
    </div>
</body>
</html>
