<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bon de transfert {{ $stockTransfer->reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #0f172a; margin: 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .title { font-size: 24px; font-weight: 700; margin: 0; }
        .muted { color: #64748b; font-size: 12px; }
        .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
        .card { border: 1px solid #cbd5e1; border-radius: 12px; padding: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 10px 8px; text-align: left; }
        th { font-size: 12px; text-transform: uppercase; color: #475569; }
        .text-right { text-align: right; }
        @media print { body { margin: 16px; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div>
            <p class="title">Bon de transfert</p>
            <div class="muted">{{ $company?->name ?? config('app.name') }}</div>
        </div>
        <div>
            <div><strong>Réf:</strong> {{ $stockTransfer->reference }}</div>
            <div><strong>Date:</strong> {{ $stockTransfer->transferred_at?->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="muted">Source</div>
            <div><strong>{{ $stockTransfer->fromLocation?->name }}</strong></div>
        </div>
        <div class="card">
            <div class="muted">Destination</div>
            <div><strong>{{ $stockTransfer->toLocation?->name }}</strong></div>
        </div>
        <div class="card">
            <div class="muted">Créé par</div>
            <div><strong>{{ $stockTransfer->createdBy?->name ?? 'Systeme' }}</strong></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th class="text-right">Quantité</th>
                <th class="text-right">Coût unitaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockTransfer->movements as $movement)
                <tr>
                    <td>{{ $movement->product?->name ?? 'Article supprimé' }}</td>
                    <td class="text-right">{{ number_format((float) $movement->quantity, 3) }}</td>
                    <td class="text-right">{{ number_format((float) $movement->unit_cost_local, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
