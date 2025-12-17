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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_title', 250)->nullable();
            $table->integer('quantity')->default(0)->nullable();
            $table->double('price', 10, 2)->default(0.00)->comment('include tax')->nullable();
            $table->double('total_price', 10, 2)->default(0.00)->comment('include tax')->nullable();
            $table->double('price_exc_tax', 10, 2)->default(0.00)->comment('exclude tax');
            $table->double('total_price_exc_tax', 10, 2)->default(0.00)->comment('exclude tax');
            $table->string('tax', 100)->nullable();
            $table->string('hsn_code', 100)->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
