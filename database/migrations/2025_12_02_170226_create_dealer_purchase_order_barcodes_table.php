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
        Schema::create('dealer_purchase_order_barcodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_purchase_order_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('barcode_no', 150)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('dealer_purchase_order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_purchase_order_barcodes');
    }
};
