<?php

namespace App\Support;

use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountingStatementBuilder
{
    public function incomeStatement(?string $start = null, ?string $end = null): array
    {
        $rows = $this->statementRows($start, $end, ['6', '7', '8']);

        $operatingRevenue = $this->section($rows, fn ($row) => $row['class'] === '7');
        $operatingExpense = $this->section($rows, fn ($row) => $row['class'] === '6');
        $otherRevenue = $this->section($rows, fn ($row) => $row['class'] === '8' && $row['type'] === 'revenue');
        $otherExpense = $this->section($rows, fn ($row) => $row['class'] === '8' && $row['type'] === 'expense');

        $totals = [
            'operating_revenue' => $this->total($operatingRevenue),
            'operating_expense' => $this->total($operatingExpense),
            'other_revenue' => $this->total($otherRevenue),
            'other_expense' => $this->total($otherExpense),
        ];

        $totals['operating_result'] = $totals['operating_revenue'] - $totals['operating_expense'];
        $totals['other_result'] = $totals['other_revenue'] - $totals['other_expense'];
        $totals['net_result'] = $totals['operating_result'] + $totals['other_result'];

        return [
            'sections' => [
                'operating_revenue' => $operatingRevenue,
                'operating_expense' => $operatingExpense,
                'other_revenue' => $otherRevenue,
                'other_expense' => $otherExpense,
            ],
            'totals' => $totals,
        ];
    }

    public function balanceSheet(?string $start = null, ?string $end = null): array
    {
        $rows = $this->balanceRows($end);
        $incomeStatement = $this->incomeStatement($start, $end);
        $periodResult = (float) $incomeStatement['totals']['net_result'];

        $stableAssets = $this->section($rows, fn ($row) => in_array($row['class'], ['1', '2'], true) && $row['side'] === 'asset');
        $inventoryAssets = $this->section($rows, fn ($row) => $row['class'] === '3' && $row['side'] === 'asset');
        $receivablesAssets = $this->section($rows, fn ($row) => in_array($row['class'], ['4', '5'], true) && $row['side'] === 'asset');

        $equity = $this->section($rows, fn ($row) => $row['type'] === 'equity');
        if (abs($periodResult) > 0.0001) {
            $equity->push([
                'number' => '13*',
                'name' => 'Resultat net de la periode',
                'type' => 'equity',
                'class' => '1',
                'amount' => $periodResult,
                'side' => 'liability',
            ]);
        }

        $longTermLiabilities = $this->section($rows, fn ($row) => $row['class'] === '1' && $row['type'] === 'liability');
        $currentLiabilities = $this->section($rows, fn ($row) => in_array($row['class'], ['4', '5'], true) && $row['type'] === 'liability');

        $totals = [
            'stable_assets' => $this->total($stableAssets),
            'inventory_assets' => $this->total($inventoryAssets),
            'receivables_assets' => $this->total($receivablesAssets),
            'equity' => $this->total($equity),
            'long_term_liabilities' => $this->total($longTermLiabilities),
            'current_liabilities' => $this->total($currentLiabilities),
        ];

        $totals['assets_total'] = $totals['stable_assets'] + $totals['inventory_assets'] + $totals['receivables_assets'];
        $totals['liabilities_total'] = $totals['equity'] + $totals['long_term_liabilities'] + $totals['current_liabilities'];
        $totals['difference'] = $totals['assets_total'] - $totals['liabilities_total'];

        return [
            'sections' => [
                'stable_assets' => $stableAssets,
                'inventory_assets' => $inventoryAssets,
                'receivables_assets' => $receivablesAssets,
                'equity' => $equity,
                'long_term_liabilities' => $longTermLiabilities,
                'current_liabilities' => $currentLiabilities,
            ],
            'totals' => $totals,
            'income_statement' => $incomeStatement,
        ];
    }

    private function statementRows(?string $start, ?string $end, array $classes): Collection
    {
        return $this->query($start, $end, $classes)
            ->get()
            ->map(function ($row) {
                $debit = (float) ($row->total_debit ?? 0);
                $credit = (float) ($row->total_credit ?? 0);
                $amount = in_array($row->type, ['revenue', 'equity', 'liability'], true)
                    ? $credit - $debit
                    : $debit - $credit;

                return [
                    'number' => $row->number,
                    'name' => $row->name,
                    'type' => $row->type,
                    'class' => substr((string) $row->number, 0, 1),
                    'amount' => $amount,
                ];
            })
            ->filter(fn ($row) => abs($row['amount']) > 0.0001)
            ->values();
    }

    private function balanceRows(?string $end): Collection
    {
        return $this->query(null, $end, ['1', '2', '3', '4', '5'])
            ->get()
            ->map(function ($row) {
                $debit = (float) ($row->total_debit ?? 0);
                $credit = (float) ($row->total_credit ?? 0);
                $isAsset = in_array($row->type, ['asset', 'expense'], true);
                $amount = $isAsset ? $debit - $credit : $credit - $debit;

                return [
                    'number' => $row->number,
                    'name' => $row->name,
                    'type' => $row->type,
                    'class' => substr((string) $row->number, 0, 1),
                    'amount' => $amount,
                    'side' => $isAsset ? 'asset' : 'liability',
                ];
            })
            ->filter(fn ($row) => abs($row['amount']) > 0.0001)
            ->values();
    }

    private function section(Collection $rows, callable $filter): Collection
    {
        return $rows
            ->filter($filter)
            ->sortBy('number')
            ->values();
    }

    private function total(Collection $rows): float
    {
        return (float) $rows->sum('amount');
    }

    private function query(?string $start, ?string $end, array $classes): Builder
    {
        return JournalEntryLine::query()
            ->select([
                'accounts.id',
                'accounts.number',
                'accounts.name',
                'accounts.type',
                DB::raw('SUM(journal_entry_lines.debit) as total_debit'),
                DB::raw('SUM(journal_entry_lines.credit) as total_credit'),
            ])
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where(function (Builder $query) use ($classes) {
                foreach ($classes as $class) {
                    $query->orWhere('accounts.number', 'like', $class . '%');
                }
            })
            ->whereHas('entry', function (Builder $query) use ($start, $end) {
                if ($start) {
                    $query->whereDate('entry_date', '>=', $start);
                }

                if ($end) {
                    $query->whereDate('entry_date', '<=', $end);
                }
            })
            ->groupBy('accounts.id', 'accounts.number', 'accounts.name', 'accounts.type')
            ->orderBy('accounts.number');
    }
}
