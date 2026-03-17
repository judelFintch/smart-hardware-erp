<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE purchase_orders MODIFY status ENUM('commande','en_cours','en_fabrication','livree_agence','en_transit','receptionnee','approvisionnee') DEFAULT 'en_cours'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE purchase_orders ALTER COLUMN status TYPE VARCHAR(50)");
            DB::statement("ALTER TABLE purchase_orders ALTER COLUMN status SET DEFAULT 'en_cours'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE purchase_orders MODIFY status ENUM('en_cours','en_transit','receptionnee') DEFAULT 'en_cours'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE purchase_orders ALTER COLUMN status TYPE VARCHAR(50)");
            DB::statement("ALTER TABLE purchase_orders ALTER COLUMN status SET DEFAULT 'en_cours'");
        }
    }
};
