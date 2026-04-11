<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE products MODIFY purchase_price DECIMAL(10,2) NULL');
        DB::statement('ALTER TABLE products MODIFY sale_price DECIMAL(10,2) NULL');
        DB::statement('ALTER TABLE products MODIFY quantity INT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('UPDATE products SET purchase_price = 0 WHERE purchase_price IS NULL');
        DB::statement('UPDATE products SET sale_price = 0 WHERE sale_price IS NULL');
        DB::statement('UPDATE products SET quantity = 0 WHERE quantity IS NULL');

        DB::statement('ALTER TABLE products MODIFY purchase_price DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE products MODIFY sale_price DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE products MODIFY quantity INT NOT NULL');
    }
};
