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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['local', 'foreign'])->default('local');
            $table->enum('status', ['en_cours', 'en_transit', 'receptionnee'])->default('en_cours');
            $table->string('reference')->nullable();
            $table->date('ordered_at')->nullable();
            $table->date('in_transit_at')->nullable();
            $table->date('received_at')->nullable();
            $table->string('currency')->default('CDF');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('subtotal_foreign', 15, 2)->default(0);
            $table->decimal('subtotal_local', 15, 2)->default(0);
            $table->decimal('accessory_fees_local', 15, 2)->default(0);
            $table->decimal('transport_fees_local', 15, 2)->default(0);
            $table->decimal('total_cost_local', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
