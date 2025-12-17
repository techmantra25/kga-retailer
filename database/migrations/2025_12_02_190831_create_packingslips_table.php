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
        Schema::create('packingslips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->string('slipno', 100)->nullable();
            $table->enum('goods_out_type', ['scan', 'bulk'])->default('scan');
            $table->tinyInteger('is_goods_out')->default(0)->comment('bulk - 1; scan - 0; initially');
            $table->longText('details')->nullable()->comment('product details json encode array');
            $table->string('invoice_no', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packingslips');
    }
};
