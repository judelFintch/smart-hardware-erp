<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('name')->constrained('units')->nullOnDelete();
        });

        if (Schema::hasColumn('products', 'unit')) {
            $unitRows = DB::table('units')->get()->keyBy('code');

            DB::table('products')->orderBy('id')->chunkById(100, function ($products) use ($unitRows) {
                foreach ($products as $product) {
                    $code = strtolower((string) $product->unit);
                    $unitId = $unitRows[$code]->id ?? null;
                    if ($unitId) {
                        DB::table('products')->where('id', $product->id)->update(['unit_id' => $unitId]);
                    }
                }
            });

            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('unit');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit')->default('pcs')->after('name');
            $table->dropConstrainedForeignId('unit_id');
        });
    }
};
