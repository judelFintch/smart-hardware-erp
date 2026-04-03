<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportBatchRow extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'import_batch_id',
        'row_number',
        'sku',
        'barcode',
        'action',
        'reason',
        'product_id',
        'payload',
        'meta',
    ];

    protected $casts = [
        'payload' => 'array',
        'meta' => 'array',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class, 'import_batch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
