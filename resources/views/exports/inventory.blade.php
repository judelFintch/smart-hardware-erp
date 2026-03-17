<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Export Inventaires</title>
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
        <h1>Inventaires</h1>
        @if ($company)
            <div class="muted">{{ $company->name }}</div>
            <div class="muted">{{ $company->address }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Inventaire</th>
                    <th>Date</th>
                    <th>Magasin</th>
                    <th>Article</th>
                    <th>Système</th>
                    <th>Compté</th>
                    <th>Différence</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $missingQty = 0;
                    $missingValue = 0;
                    $surplusQty = 0;
                    $surplusValue = 0;
                @endphp
                @foreach ($items as $item)
                    @php
                        $diff = (float) $item->difference;
                        $value = $diff * (float) $item->unit_cost_local;
                        if ($diff < 0) {
                            $missingQty += abs($diff);
                            $missingValue += abs($value);
                        } elseif ($diff > 0) {
                            $surplusQty += $diff;
                            $surplusValue += $value;
                        }
                    @endphp
                    <tr>
                        <td>{{ $item->inventory_count_id }}</td>
                        <td>{{ optional($item->inventoryCount?->counted_at)->format('d/m/Y') }}</td>
                        <td>{{ $item->inventoryCount?->location?->name }}</td>
                        <td>{{ $item->product?->name }}</td>
                        <td>{{ number_format($item->system_quantity, 3) }}</td>
                        <td>{{ number_format($item->counted_quantity, 3) }}</td>
                        <td>{{ number_format($item->difference, 3) }}</td>
                        <td>{{ number_format($value, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7"><strong>Totaux manquants</strong></td>
                    <td><strong>{{ number_format($missingQty, 3) }} / {{ number_format($missingValue, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="7"><strong>Totaux surplus</strong></td>
                    <td><strong>{{ number_format($surplusQty, 3) }} / {{ number_format($surplusValue, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
