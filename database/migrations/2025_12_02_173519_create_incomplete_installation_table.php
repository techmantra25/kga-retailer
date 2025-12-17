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
        Schema::create('incomplete_installation', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('cat_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->string('item')->nullable();
            $table->string('class_name')->nullable();
            $table->date('bill_date')->nullable();
            $table->string('bill_no')->nullable();
            $table->string('barcode')->nullable();
            $table->string('serial')->nullable();
            $table->string('branch', 250)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('address')->nullable();
            $table->string('near_location')->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->unsignedBigInteger('installation_id')->nullable()->comment('if installation request generated');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('installation_id')->references('id')->on('installations')->onDelete('cascade');
            $table->foreign('cat_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('service_partner_id')->references('id')->on('service_partners')->onDelete('cascade');

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomplete_installation');
    }
};
