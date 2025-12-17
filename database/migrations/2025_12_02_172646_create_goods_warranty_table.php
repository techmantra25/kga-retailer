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
        Schema::create('goods_warranty', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->nullable();
            $table->enum('dealer_type', ['khosla', 'nonkhosla'])->nullable();
            $table->enum('warranty_type', ['general', 'categorized'])->nullable();
            $table->integer('general_warranty')->nullable();
            $table->integer('comprehensive_warranty')->nullable();
            $table->integer('extra_warranty')->nullable();
            $table->integer('motor_warranty')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('goods_id')->references('id')->on('products')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_warranty');
    }
};
