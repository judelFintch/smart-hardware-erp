<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCountItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inventory_count_id',
        'product_id',
        'counted_quantity',
        'system_quantity',
        'difference',
        'unit_cost_local',
        'unit_sale_price_local',
    ];

    public function inventoryCount(): BelongsTo
    {
        return $this->belongsTo(InventoryCount::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
