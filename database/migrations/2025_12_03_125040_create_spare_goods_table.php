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
        Schema::create('spare_goods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('spare_id')->nullable();
            $table->unsignedBigInteger('goods_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('spare_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('goods_id')->references('id')->on('products')->onDelete('cascade');

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_goods');
    }
};
