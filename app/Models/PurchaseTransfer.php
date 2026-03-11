<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseTransfer extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'amount_foreign',
        'amount_local',
        'paid_at',
        'reference',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
