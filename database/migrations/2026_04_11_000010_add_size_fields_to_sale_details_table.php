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
        Schema::table('sale_details', function (Blueprint $table) {
            $table->uuid('product_size_id')->nullable()->after('product_id');
            $table->string('size_name', 20)->nullable()->after('product_size_id');

            $table->foreign('product_size_id')
                ->references('id')
                ->on('product_sizes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropForeign(['product_size_id']);
            $table->dropColumn(['product_size_id', 'size_name']);
        });
    }
};
