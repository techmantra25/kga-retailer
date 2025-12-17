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
        Schema::create('dap_spear_part_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dap_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('title', 255)->nullable();
            $table->double('price', 10, 2)->default(0.00);
            $table->string('profit_percentage', 250);
            $table->double('final_amount', 10, 2)->default(0.00);
            $table->integer('warranty_status')->default(0)->comment('0:False, 1: True');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('dap_id')->references('id')->on('dap_services')->onDelete('cascade');
         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_spear_part_orders');
    }
};
