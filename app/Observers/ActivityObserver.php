<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ActivityObserver
{
    public function created(Model $model): void
    {
        $this->record('created', $model);
    }

    public function updated(Model $model): void
    {
        $this->record('updated', $model);
    }

    public function deleted(Model $model): void
    {
        $this->record('deleted', $model);
    }

    private function record(string $action, Model $model): void
    {
        if ($model instanceof ActivityLog) {
            return;
        }

        $changes = $this->resolveChanges($action, $model);
        if ($action === 'updated' && empty($changes)) {
            return;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $model::class,
            'subject_id' => $model->getKey(),
            'description' => $this->buildDescription($action, $model, $changes),
            'meta' => [
                'changes' => $changes,
                'subject' => $this->subjectContext($model),
            ],
        ]);
    }

    private function resolveChanges(string $action, Model $model): array
    {
        if ($action === 'created') {
            return collect($model->getAttributes())
                ->except($this->ignoredColumns())
                ->mapWithKeys(fn ($value, $field) => [$field => [
                    'old' => null,
                    'new' => $this->normalizeValue($value),
                ]])
                ->all();
        }

        if ($action === 'updated') {
            return collect($model->getChanges())
                ->except($this->ignoredColumns())
                ->mapWithKeys(fn ($value, $field) => [$field => [
                    'old' => $this->normalizeValue($model->getOriginal($field)),
                    'new' => $this->normalizeValue($model->getAttribute($field)),
                ]])
                ->all();
        }

        return collect($model->getAttributes())
            ->except($this->ignoredColumns())
            ->mapWithKeys(fn ($value, $field) => [$field => [
                'old' => $this->normalizeValue($value),
                'new' => null,
            ]])
            ->all();
    }

    private function buildDescription(string $action, Model $model, array $changes): string
    {
        $label = Str::headline(class_basename($model));
        $subject = $this->subjectContext($model);
        $subjectSuffix = isset($subject['parent_label'], $subject['parent_id'])
            ? " · {$subject['parent_label']} #{$subject['parent_id']}"
            : '';

        if ($action === 'updated' && !empty($changes)) {
            $fields = collect(array_keys($changes))
                ->take(3)
                ->map(fn ($field) => Str::headline($field))
                ->implode(', ');

            return "{$label} #{$model->getKey()} modifié{$subjectSuffix} · {$fields}";
        }

        if ($action === 'created') {
            return "{$label} #{$model->getKey()} créé{$subjectSuffix}";
        }

        return "{$label} #{$model->getKey()} supprimé{$subjectSuffix}";
    }

    private function subjectContext(Model $model): array
    {
        return match (true) {
            method_exists($model, 'purchaseOrder') && $model->purchaseOrder()->exists() => [
                'parent_label' => 'Achat',
                'parent_id' => $model->purchaseOrder?->id,
            ],
            method_exists($model, 'sale') && $model->sale()->exists() => [
                'parent_label' => 'Vente',
                'parent_id' => $model->sale?->id,
            ],
            method_exists($model, 'inventoryCount') && $model->inventoryCount()->exists() => [
                'parent_label' => 'Inventaire',
                'parent_id' => $model->inventoryCount?->id,
            ],
            default => [],
        };
    }

    private function ignoredColumns(): array
    {
        return [
            'updated_at',
            'created_at',
            'deleted_at',
        ];
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (is_scalar($value) || $value === null) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return Arr::wrap($value);
    }
}
