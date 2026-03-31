<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleAdjustment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_id',
        'location_id',
        'original_product_id',
        'original_quantity',
        'original_unit_price',
        'replacement_product_id',
        'replacement_quantity',
        'replacement_unit_price',
        'type',
        'item_condition',
        'amount_local',
        'notes',
        'processed_at',
        'created_by',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }

    public function originalProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'original_product_id');
    }

    public function replacementProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'replacement_product_id');
    }
}
