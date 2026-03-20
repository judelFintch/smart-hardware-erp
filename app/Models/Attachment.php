<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'attachable_type',
        'attachable_id',
        'created_by',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
