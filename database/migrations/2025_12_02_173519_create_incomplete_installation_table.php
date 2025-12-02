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

            $table->index('product_id');
            $table->index('installation_id');
            $table->index('cat_id');
            $table->index('service_partner_id');
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
