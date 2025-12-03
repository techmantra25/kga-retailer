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
        Schema::create('purchase_order_remove_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->double('cost_price', 10, 2);
            $table->integer('pack_of')->default(0);
            $table->integer('quantity_in_pack')->default(0);
            $table->integer('quantity')->default(0);
            $table->double('total_price', 10, 2);
            $table->unsignedBigInteger('removed_by')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('purchase_order_id');
            $table->index('product_id');
            $table->index('removed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_remove_items');
    }
};
