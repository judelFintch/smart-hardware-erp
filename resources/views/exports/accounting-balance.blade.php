<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Balance comptable</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
            h1 { font-size: 18px; margin-bottom: 8px; }
            table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            th, td { border: 1px solid #e2e8f0; padding: 6px; text-align: left; }
            th { background: #f8fafc; }
            .muted { color: #64748b; font-size: 11px; }
            .right { text-align: right; }
        </style>
    </head>
    <body>
        <h1>Balance comptable</h1>
        @if ($company)
            <div class="muted">{{ $company->name }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Compte</th>
                    <th>Libelle</th>
                    <th>Type</th>
                    <th class="right">Debit</th>
                    <th class="right">Credit</th>
                    <th class="right">Solde</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>{{ $row->number }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->type }}</td>
                        <td class="right">{{ number_format((float) $row->total_debit, 2) }}</td>
                        <td class="right">{{ number_format((float) $row->total_credit, 2) }}</td>
                        <td class="right">{{ number_format((float) $row->balance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <td>Total debit</td>
                    <td class="right">{{ number_format((float) $totals['debit'], 2) }}</td>
                </tr>
                <tr>
                    <td>Total credit</td>
                    <td class="right">{{ number_format((float) $totals['credit'], 2) }}</td>
                </tr>
                <tr>
                    <td>Solde net</td>
                    <td class="right">{{ number_format((float) $totals['balance'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
