<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnsTable extends Migration
{
    public function up()
    {
        Schema::create('return_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_id');
            $table->uuid('sale_detail_id');
            $table->uuid('user_id');
            $table->integer('quantity')->default(1);
            $table->string('note')->nullable();
            $table->string('reason')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('created_at')->useCurrent();

            // Relaciones
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('sale_detail_id')->references('id')->on('sale_details')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('return_items');
    }

}