<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Grand livre</title>
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
        <h1>Grand livre</h1>
        @if ($company)
            <div class="muted">{{ $company->name }}</div>
        @endif
        <div class="muted">
            Compte:
            @if ($selectedAccount)
                {{ $selectedAccount->number }} · {{ $selectedAccount->name }}
            @else
                Tous les comptes
            @endif
            · Solde initial: {{ number_format((float) $openingBalance, 2) }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Compte</th>
                    <th>Journal</th>
                    <th>Reference</th>
                    <th>Libelle</th>
                    <th class="right">Debit</th>
                    <th class="right">Credit</th>
                    <th class="right">Solde</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lines as $line)
                    <tr>
                        <td>{{ $line->entry?->entry_date?->format('d/m/Y') }}</td>
                        <td>{{ $line->account?->number }} {{ $line->account?->name }}</td>
                        <td>{{ $line->entry?->journal?->code }}</td>
                        <td>{{ $line->entry?->reference }}</td>
                        <td>{{ $line->description ?: $line->entry?->description }}</td>
                        <td class="right">{{ number_format((float) $line->debit, 2) }}</td>
                        <td class="right">{{ number_format((float) $line->credit, 2) }}</td>
                        <td class="right">{{ number_format((float) $line->running_balance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
