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
        Schema::create('dealer_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 150)->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->double('amount', 10, 2)->default(0.00);
            $table->boolean('is_goods_in')->default(0);
            $table->boolean('is_cancelled')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('dealer_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_purchase_orders');
    }
};
