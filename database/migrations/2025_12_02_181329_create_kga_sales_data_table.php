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
        Schema::create('kga_sales_data', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('cat_id')->nullable();
            $table->date('bill_date')->nullable();
            $table->string('bill_no', 255)->nullable();
            $table->string('item', 255)->nullable();
            $table->string('brand', 255)->nullable();
            $table->string('class_name', 255)->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('near_location', 255)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('barcode', 150)->nullable();
            $table->string('serial', 100)->nullable();
            $table->string('branch', 250)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->integer('amc_id')->nullable()->comment("FROM 'product_amcs' table Id");
            $table->tinyInteger('is_covered')->default(0)->comment('is task(cron) covered');

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('cat_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kga_sales_data');
    }
};
