<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
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
