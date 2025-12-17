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
        Schema::create('kga_stock_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('stock_date')->nullable();
            $table->string('sitecode', 250)->nullable();
            $table->string('dealer', 250)->nullable();
            $table->string('sitecode_info', 250)->nullable();
            $table->string('itemcode', 250)->nullable();
            $table->string('itemdesc', 250)->nullable();
            $table->string('product_class_name', 250)->nullable();
            $table->string('opening', 20)->nullable();
            $table->string('received', 20)->nullable();
            $table->string('issued', 250)->nullable();
            $table->string('closing', 20)->nullable();
            $table->string('available', 20)->nullable();
            $table->string('defective', 20)->nullable();
            $table->string('display', 20)->nullable();
            $table->string('transit', 20)->nullable();
            $table->string('defective_transit', 20)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');

        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kga_stock_data');
    }
};
