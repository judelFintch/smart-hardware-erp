<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_count_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('counted_quantity', 15, 3);
            $table->decimal('system_quantity', 15, 3)->default(0);
            $table->decimal('difference', 15, 3)->default(0);
            $table->decimal('unit_cost_local', 15, 2)->default(0);
            $table->decimal('unit_sale_price_local', 15, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['inventory_count_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_count_items');
    }
};
