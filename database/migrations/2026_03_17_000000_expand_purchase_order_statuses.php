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
        } elseif ($driver === 'sqlite') {
            DB::statement("
                CREATE TABLE purchase_orders_new (
                    id integer primary key autoincrement not null,
                    supplier_id integer not null,
                    type varchar check (type in ('local', 'foreign')) not null default 'local',
                    status varchar check (status in ('commande', 'en_cours', 'en_fabrication', 'livree_agence', 'en_transit', 'receptionnee', 'approvisionnee')) not null default 'en_cours',
                    reference varchar,
                    ordered_at date,
                    in_transit_at date,
                    received_at date,
                    currency varchar not null default 'CDF',
                    exchange_rate numeric not null default '1',
                    subtotal_foreign numeric not null default '0',
                    subtotal_local numeric not null default '0',
                    accessory_fees_local numeric not null default '0',
                    transport_fees_local numeric not null default '0',
                    total_cost_local numeric not null default '0',
                    notes text,
                    created_by integer,
                    created_at datetime,
                    updated_at datetime,
                    foreign key(supplier_id) references suppliers(id) on delete cascade,
                    foreign key(created_by) references users(id) on delete set null
                )
            ");
            DB::statement("INSERT INTO purchase_orders_new SELECT * FROM purchase_orders");
            DB::statement("DROP TABLE purchase_orders");
            DB::statement("ALTER TABLE purchase_orders_new RENAME TO purchase_orders");
            DB::statement("CREATE INDEX purchase_orders_type_status_index on purchase_orders (type, status)");
            DB::statement("CREATE INDEX purchase_orders_supplier_id_index on purchase_orders (supplier_id)");
            DB::statement("CREATE INDEX purchase_orders_ordered_at_index on purchase_orders (ordered_at)");
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
        } elseif ($driver === 'sqlite') {
            DB::statement("UPDATE purchase_orders SET status = 'en_cours' WHERE status NOT IN ('en_cours', 'en_transit', 'receptionnee')");
            DB::statement("
                CREATE TABLE purchase_orders_old (
                    id integer primary key autoincrement not null,
                    supplier_id integer not null,
                    type varchar check (type in ('local', 'foreign')) not null default 'local',
                    status varchar check (status in ('en_cours', 'en_transit', 'receptionnee')) not null default 'en_cours',
                    reference varchar,
                    ordered_at date,
                    in_transit_at date,
                    received_at date,
                    currency varchar not null default 'CDF',
                    exchange_rate numeric not null default '1',
                    subtotal_foreign numeric not null default '0',
                    subtotal_local numeric not null default '0',
                    accessory_fees_local numeric not null default '0',
                    transport_fees_local numeric not null default '0',
                    total_cost_local numeric not null default '0',
                    notes text,
                    created_by integer,
                    created_at datetime,
                    updated_at datetime,
                    foreign key(supplier_id) references suppliers(id) on delete cascade,
                    foreign key(created_by) references users(id) on delete set null
                )
            ");
            DB::statement("INSERT INTO purchase_orders_old SELECT * FROM purchase_orders");
            DB::statement("DROP TABLE purchase_orders");
            DB::statement("ALTER TABLE purchase_orders_old RENAME TO purchase_orders");
            DB::statement("CREATE INDEX purchase_orders_type_status_index on purchase_orders (type, status)");
            DB::statement("CREATE INDEX purchase_orders_supplier_id_index on purchase_orders (supplier_id)");
            DB::statement("CREATE INDEX purchase_orders_ordered_at_index on purchase_orders (ordered_at)");
        }
    }
};
