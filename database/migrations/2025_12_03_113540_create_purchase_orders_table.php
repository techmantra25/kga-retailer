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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('created_by');
            $table->string('order_no', 100)->default('')->nullable();
            $table->string('grn_no', 255)->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->enum('type', ['fg', 'sp'])->default('fg')->nullable();
            $table->double('amount', 10, 2)->default(0.00)->comment('total po amount')->nullable();
            $table->longText('details')->nullable();
            $table->tinyInteger('is_goods_in')->default(0);
            $table->enum('goods_in_type', ['bulk', 'scan'])->default('scan');
            $table->tinyInteger('status')->default(1)->comment('1:Pending;2:Received;3:Cancelled');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('created_by');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
