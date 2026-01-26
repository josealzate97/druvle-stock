<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('code', 20);
            $table->uuid('category_id');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->integer('quantity')->length(10);
            $table->uuid('provider_id')->nullable();
            $table->string('notes', 250)->nullable();
            $table->boolean('taxable')->default(false);
            $table->uuid('tax_id')->nullable();
            $table->boolean('status')->default(true);
            $table->dateTime('creation_date')->nullable();
            $table->dateTime('update_date')->nullable();
            $table->dateTime('delete_date')->nullable();

            // Relaciones
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('set null');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
