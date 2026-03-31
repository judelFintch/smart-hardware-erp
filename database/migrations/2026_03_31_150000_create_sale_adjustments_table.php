<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('stock_locations')->cascadeOnDelete();
            $table->foreignId('original_product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('original_quantity', 15, 3);
            $table->decimal('original_unit_price', 15, 2)->default(0);
            $table->foreignId('replacement_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('replacement_quantity', 15, 3)->nullable();
            $table->decimal('replacement_unit_price', 15, 2)->nullable();
            $table->enum('type', ['return', 'exchange']);
            $table->enum('item_condition', ['good', 'damaged', 'broken', 'defective', 'other'])->default('good');
            $table->decimal('amount_local', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->useCurrent();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['sale_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_adjustments');
    }
};
