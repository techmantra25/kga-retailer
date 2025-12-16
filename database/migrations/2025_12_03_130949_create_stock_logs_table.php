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
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('packingslip_id')->nullable();
            $table->unsignedBigInteger('return_spare_id')->nullable();
            $table->unsignedBigInteger('dealer_purchase_order_id')->nullable();
            $table->bigInteger('quantity')->default(0);
            $table->bigInteger('data_id')->nullable();
            $table->enum('type', ['in', 'out'])->default('in');
            $table->enum('entry_type', ['grn', 'ps'])->default('grn');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('product_id');
            $table->index('packingslip_id');
            $table->index('purchase_order_id');
            $table->index('dealer_purchase_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
