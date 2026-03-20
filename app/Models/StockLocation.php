<?php

namespace App\Models;

use App\Models\Concerns\PreservesUniqueValuesOnSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLocation extends Model
{
    use PreservesUniqueValuesOnSoftDelete, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'notes',
    ];

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'location_id');
    }

    public function stockMovementsFrom(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'from_location_id');
    }

    public function stockMovementsTo(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'to_location_id');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'location_id');
    }

    protected function uniqueSoftDeleteColumns(): array
    {
        return ['code'];
    }
}
