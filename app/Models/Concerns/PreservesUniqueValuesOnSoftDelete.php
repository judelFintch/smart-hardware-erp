<?php

namespace App\Models\Concerns;

trait PreservesUniqueValuesOnSoftDelete
{
    abstract protected function uniqueSoftDeleteColumns(): array;

    public static function bootPreservesUniqueValuesOnSoftDelete(): void
    {
        static::deleted(function ($model): void {
            if (!method_exists($model, 'trashed') || !$model->trashed() || $model->isForceDeleting()) {
                return;
            }

            $updates = [];

            foreach ($model->uniqueSoftDeleteColumns() as $column) {
                $value = $model->getAttribute($column);

                if ($value === null || $value === '') {
                    continue;
                }

                $prefix = static::softDeleteUniquePrefix($model->getKey(), $column);

                if (str_starts_with((string) $value, $prefix)) {
                    continue;
                }

                $updates[$column] = $prefix . $value;
            }

            if ($updates !== []) {
                $model->forceFill($updates)->saveQuietly();
            }
        });

        static::restoring(function ($model): void {
            foreach ($model->uniqueSoftDeleteColumns() as $column) {
                $value = $model->getAttribute($column);

                if (!is_string($value)) {
                    continue;
                }

                $prefix = static::softDeleteUniquePrefix($model->getKey(), $column);

                if (str_starts_with($value, $prefix)) {
                    $model->{$column} = substr($value, strlen($prefix));
                }
            }
        });
    }

    protected static function softDeleteUniquePrefix(mixed $key, string $column): string
    {
        return '__trashed__' . $column . '__' . $key . '__';
    }
}
