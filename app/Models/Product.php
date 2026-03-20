<?php

namespace App\Models;

use App\Models\Concerns\PreservesUniqueValuesOnSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use PreservesUniqueValuesOnSoftDelete, SoftDeletes;

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'unit_id',
        'description',
        'avg_cost_local',
        'sale_price_local',
        'sale_margin_percent',
        'reorder_level',
        'is_active',
    ];

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryCountItems(): HasMany
    {
        return $this->hasMany(InventoryCountItem::class);
    }

    protected function uniqueSoftDeleteColumns(): array
    {
        return ['sku', 'barcode'];
    }
}
