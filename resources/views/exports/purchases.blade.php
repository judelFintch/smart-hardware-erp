<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Export Achats</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
            h1 { font-size: 18px; margin-bottom: 8px; }
            table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            th, td { border: 1px solid #e2e8f0; padding: 6px; text-align: left; }
            th { background: #f8fafc; }
            .muted { color: #64748b; font-size: 11px; }
        </style>
    </head>
    <body>
        <h1>Achats</h1>
        @if ($company)
            <div class="muted">{{ $company->name }}</div>
            <div class="muted">{{ $company->address }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fournisseur</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Total</th>
                    <th>Commande</th>
                    <th>Réception</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->id }}</td>
                        <td>{{ $purchase->supplier?->name }}</td>
                        <td>{{ $purchase->type }}</td>
                        <td>{{ $purchase->status }}</td>
                        <td>{{ number_format($purchase->total_cost_local, 2) }}</td>
                        <td>{{ optional($purchase->ordered_at)->format('d/m/Y') }}</td>
                        <td>{{ optional($purchase->received_at)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
