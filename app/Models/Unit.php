<?php

namespace App\Models;

use App\Models\Concerns\PreservesUniqueValuesOnSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use PreservesUniqueValuesOnSoftDelete, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected function uniqueSoftDeleteColumns(): array
    {
        return ['code'];
    }
}
