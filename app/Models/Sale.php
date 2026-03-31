<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'type',
        'status',
        'subtotal',
        'discount_total',
        'total_amount',
        'paid_total',
        'sold_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(SaleAdjustment::class)->latest('processed_at')->latest('id');
    }
}
