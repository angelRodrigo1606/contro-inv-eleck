<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite does not support ADD CONSTRAINT CHECK on ALTER TABLE.
            // Use INSTEAD OF triggers to enforce the rule.
            DB::statement('
                CREATE TRIGGER products_quantity_non_negative_check_insert
                BEFORE INSERT ON products
                FOR EACH ROW
                WHEN NEW.quantity < 0
                BEGIN
                    SELECT RAISE(ABORT, "CHECK constraint violation: quantity >= 0");
                END
            ');

            DB::statement('
                CREATE TRIGGER products_quantity_non_negative_check_update
                BEFORE UPDATE ON products
                FOR EACH ROW
                WHEN NEW.quantity < 0
                BEGIN
                    SELECT RAISE(ABORT, "CHECK constraint violation: quantity >= 0");
                END
            ');

            return;
        }

        // MySQL >= 8.0.16, MariaDB >= 10.2.1, PostgreSQL
        DB::statement('ALTER TABLE products ADD CONSTRAINT products_quantity_non_negative_check CHECK (quantity >= 0)');
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS products_quantity_non_negative_check_insert');
            DB::statement('DROP TRIGGER IF EXISTS products_quantity_non_negative_check_update');

            return;
        }

        DB::statement('ALTER TABLE products DROP CONSTRAINT IF EXISTS products_quantity_non_negative_check');
    }
};
