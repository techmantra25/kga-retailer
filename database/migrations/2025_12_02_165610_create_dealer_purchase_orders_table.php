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

            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

         
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
