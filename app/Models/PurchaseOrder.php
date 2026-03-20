<?php

namespace App\Models;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'type',
        'status',
        'reference',
        'ordered_at',
        'in_transit_at',
        'received_at',
        'currency',
        'exchange_rate',
        'subtotal_foreign',
        'subtotal_local',
        'accessory_fees_local',
        'transport_fees_local',
        'total_cost_local',
        'notes',
        'receive_location_id',
        'created_by',
    ];

    protected $casts = [
        'ordered_at' => 'date',
        'in_transit_at' => 'date',
        'received_at' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receiveLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'receive_location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(PurchaseTransfer::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
