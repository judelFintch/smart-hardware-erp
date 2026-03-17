<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Export Ventes</title>
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
        <h1>Ventes</h1>
        @if ($company)
            <div class="muted">{{ $company->name }}</div>
            <div class="muted">{{ $company->address }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Total</th>
                    <th>Payé</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->customer?->name }}</td>
                        <td>{{ $sale->type }}</td>
                        <td>{{ $sale->status }}</td>
                        <td>{{ number_format($sale->total_amount, 2) }}</td>
                        <td>{{ number_format($sale->paid_total, 2) }}</td>
                        <td>{{ optional($sale->sold_at)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
