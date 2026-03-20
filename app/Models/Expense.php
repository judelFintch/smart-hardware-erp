<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category',
        'description',
        'amount',
        'spent_at',
        'reference',
        'created_by',
    ];

    protected $casts = [
        'spent_at' => 'date',
    ];
}
