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
        Schema::create('customer_amc_packages', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('request_id')->nullable();
            $table->string('customer_name', 250)->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->double('amount', 10, 2)->nullable();
            $table->string('month_val', 50)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('cashfree_customer_id', 250)->nullable();
            $table->string('cashfree_order_id', 250)->nullable();
            $table->longText('json_response1')->nullable();
            $table->longText('json_response2')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Foreign keys
            $table->foreign('request_id')->references('id')->on('customer_amc_requests')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_amc_packages');
    }
};
