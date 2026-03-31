<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'currency_symbol')) {
                $table->string('currency_symbol', 10)->default('FC')->after('currency');
            }
            if (!Schema::hasColumn('company_settings', 'timezone')) {
                $table->string('timezone')->default(config('app.timezone', 'UTC'))->after('currency_symbol');
            }
            if (!Schema::hasColumn('company_settings', 'date_format')) {
                $table->string('date_format', 20)->default('d/m/Y')->after('timezone');
            }
            if (!Schema::hasColumn('company_settings', 'purchase_prefix')) {
                $table->string('purchase_prefix', 20)->default('ACH')->after('date_format');
            }
            if (!Schema::hasColumn('company_settings', 'sale_prefix')) {
                $table->string('sale_prefix', 20)->default('VTE')->after('purchase_prefix');
            }
            if (!Schema::hasColumn('company_settings', 'tax_rate')) {
                $table->decimal('tax_rate', 8, 2)->default(0)->after('sale_prefix');
            }
            if (!Schema::hasColumn('company_settings', 'low_stock_threshold')) {
                $table->decimal('low_stock_threshold', 15, 3)->default(0)->after('tax_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $columns = [
                'currency_symbol',
                'timezone',
                'date_format',
                'purchase_prefix',
                'sale_prefix',
                'tax_rate',
                'low_stock_threshold',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('company_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
