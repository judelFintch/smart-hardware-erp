<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bilan simplifié</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        h1 { margin-bottom: 4px; }
        h2 { margin: 18px 0 8px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #cbd5e1; text-align: left; }
        .right { text-align: right; }
        .muted { color: #64748b; }
    </style>
</head>
<body>
    <h1>Bilan simplifié</h1>
    <div class="muted">{{ $company?->name ?? 'Entreprise' }}</div>

    @foreach ([
        'Actifs stables' => $statement['sections']['stable_assets'],
        'Stocks et encours' => $statement['sections']['inventory_assets'],
        'Creances et tresorerie' => $statement['sections']['receivables_assets'],
        'Capitaux propres' => $statement['sections']['equity'],
        'Dettes a long terme' => $statement['sections']['long_term_liabilities'],
        'Dettes a court terme' => $statement['sections']['current_liabilities'],
    ] as $label => $rows)
        <h2>{{ $label }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Compte</th>
                    <th>Libellé</th>
                    <th class="right">Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['number'] }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td class="right">{{ number_format($row['amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">Aucun solde.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>
</html>
