<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_locations', 'is_default_sale')) {
                $table->boolean('is_default_sale')->default(false)->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_locations', function (Blueprint $table) {
            if (Schema::hasColumn('stock_locations', 'is_default_sale')) {
                $table->dropColumn('is_default_sale');
            }
        });
    }
};
