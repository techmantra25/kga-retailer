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
        Schema::create('goods_spare_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_id')->nullable();
            $table->unsignedBigInteger('spare_group_id')->nullable()->comment('subcat_id');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('goods_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('spare_group_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_spare_groups');
    }
};
