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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_location_id')->nullable()->constrained('stock_locations')->nullOnDelete();
            $table->foreignId('to_location_id')->nullable()->constrained('stock_locations')->nullOnDelete();
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_cost_local', 15, 2)->default(0);
            $table->decimal('unit_sale_price_local', 15, 2)->default(0);
            $table->enum('type', [
                'purchase_in',
                'transfer_out',
                'transfer_in',
                'sale_out',
                'return_in',
                'adjustment_in',
                'adjustment_out',
            ]);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['product_id', 'occurred_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
