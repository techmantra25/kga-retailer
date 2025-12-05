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
        Schema::create('sales_order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_orders_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(0)->nullable();
            $table->integer('delivered_quantity')->default(0);
            $table->double('product_price', 10, 2)->default(0.00)->comment('product sales price')->nullable();
            $table->double('product_total_price', 10, 2)->default(0.00)->comment('count product sales price')->nullable();
            $table->string('hsn_code', 255)->nullable();
            $table->string('tax', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('sales_orders_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_products');
    }
};
