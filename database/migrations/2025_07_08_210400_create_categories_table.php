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
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('abbreviation', 10);
            $table->string('icon', 50);
            $table->string('color', 20);
            $table->boolean('status')->default(true);
            $table->dateTime('creation_date')->nullable();
            $table->dateTime('update_date')->nullable();
            $table->dateTime('delete_date')->nullable();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
