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
        Schema::create('return_spare_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('return_spare_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity')->default(0)->nullable();
            $table->double('product_price', 10, 2)->nullable();
            $table->double('product_total_price', 10, 2)->nullable();
            $table->string('hsn_code', 50)->nullable();
            $table->string('tax', 50)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('return_spare_id')->references('id')->on('return_spare')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_spare_items');
    }
};
