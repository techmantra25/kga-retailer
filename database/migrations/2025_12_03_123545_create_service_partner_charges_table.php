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
        Schema::create('service_partner_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->double('installation', 10, 2)->nullable();
            $table->double('repair', 10, 2)->nullable();
            $table->enum('goods_type', ['general','chimney','gas_stove','ac','gieger'])->default('general');
            $table->double('cleaning', 10, 2)->nullable();
            $table->double('deep_cleaning', 10, 2)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('service_partner_id')->references('id')->on('service_partners')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_partner_charges');
    }
};
