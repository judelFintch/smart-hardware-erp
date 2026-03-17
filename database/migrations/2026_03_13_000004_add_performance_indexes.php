<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('sold_at');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('supplier_id');
            $table->index('ordered_at');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('spent_at');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('from_location_id');
            $table->index('to_location_id');
            $table->index('occurred_at');
        });

        Schema::table('stock_balances', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('location_id');
            $table->index('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['sold_at']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['ordered_at']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['spent_at']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['from_location_id']);
            $table->dropIndex(['to_location_id']);
            $table->dropIndex(['occurred_at']);
        });

        Schema::table('stock_balances', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['location_id']);
            $table->dropIndex(['quantity']);
        });
    }
};
