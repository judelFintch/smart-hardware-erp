<?php

namespace App\Livewire\Trash;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\StockLocation;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public string $type = 'all';

    /**
     * @return array<string, array{label: string, model: class-string, restorable: bool}>
     */
    private function trashMap(): array
    {
        return [
            'products' => ['label' => 'Articles', 'model' => Product::class, 'restorable' => true],
            'customers' => ['label' => 'Clients', 'model' => Customer::class, 'restorable' => true],
            'suppliers' => ['label' => 'Fournisseurs', 'model' => Supplier::class, 'restorable' => true],
            'users' => ['label' => 'Utilisateurs', 'model' => User::class, 'restorable' => true],
            'units' => ['label' => 'Unités', 'model' => Unit::class, 'restorable' => true],
            'stock_locations' => ['label' => 'Magasins & Dépôts', 'model' => StockLocation::class, 'restorable' => true],
            'expenses' => ['label' => 'Dépenses', 'model' => Expense::class, 'restorable' => true],
            'purchases' => ['label' => 'Achats', 'model' => PurchaseOrder::class, 'restorable' => true],
            'sales' => ['label' => 'Ventes', 'model' => Sale::class, 'restorable' => true],
        ];
    }

    public function restore(string $type, int $id): void
    {
        $config = $this->trashMap()[$type] ?? null;
        abort_unless($config && $config['restorable'], 404);

        $model = $config['model']::onlyTrashed()->findOrFail($id);
        $model->restore();
    }

    public function render()
    {
        $map = $this->trashMap();
        $selectedKeys = $this->type === 'all' ? array_keys($map) : [$this->type];

        $sections = collect($selectedKeys)
            ->filter(fn ($key) => isset($map[$key]))
            ->map(function ($key) use ($map) {
                $config = $map[$key];
                $query = $config['model']::onlyTrashed()->orderByDesc('deleted_at');

                if ($config['model'] === Product::class) {
                    $query->withSum('stockBalances as stock_quantity', 'quantity');
                }

                return [
                    'key' => $key,
                    'label' => $config['label'],
                    'restorable' => $config['restorable'],
                    'items' => $query->get(),
                ];
            })
            ->filter(fn (array $section) => $section['items']->isNotEmpty())
            ->values();

        $stats = [
            'types' => count($map),
            'visible_types' => $sections->count(),
            'deleted_total' => $sections->sum(fn (array $section) => $section['items']->count()),
        ];

        return view('livewire.trash.index', [
            'sections' => $sections,
            'stats' => $stats,
            'types' => $map,
        ])->layout('layouts.app');
    }
}
