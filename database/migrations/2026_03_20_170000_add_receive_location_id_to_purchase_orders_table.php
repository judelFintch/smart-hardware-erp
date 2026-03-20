<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'receive_location_id')) {
                $table->foreignId('receive_location_id')
                    ->nullable()
                    ->after('notes')
                    ->constrained('stock_locations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'receive_location_id')) {
                $table->dropConstrainedForeignId('receive_location_id');
            }
        });
    }
};
