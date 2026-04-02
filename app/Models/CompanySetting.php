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
        'login_alert_enabled',
        'login_alert_recipient',
        'login_alert_last_status',
        'login_alert_last_error',
        'login_alert_last_attempt_at',
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

    protected function casts(): array
    {
        return [
            'login_alert_enabled' => 'boolean',
            'login_alert_last_attempt_at' => 'datetime',
        ];
    }
}
