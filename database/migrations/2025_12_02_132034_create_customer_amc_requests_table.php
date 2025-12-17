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
        Schema::create('customer_amc_requests', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->string('customer_name', 250)->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->date('bill_date')->nullable();
            $table->string('bill_no', 250)->nullable();
            $table->string('barcode', 150)->nullable();
            $table->string('serial', 150)->nullable();
            $table->tinyInteger('is_availed')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_amc_requests');
    }
};
