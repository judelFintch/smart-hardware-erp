<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySetting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'legal_name',
        'tax_id',
        'phone',
        'email',
        'address',
        'currency',
        'currency_symbol',
        'timezone',
        'date_format',
        'purchase_prefix',
        'sale_prefix',
        'tax_rate',
        'low_stock_threshold',
        'logo_path',
        'invoice_footer',
    ];
}
