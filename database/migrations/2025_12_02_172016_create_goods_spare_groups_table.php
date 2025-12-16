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

            $table->index('goods_id');
            $table->index('spare_group_id');
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
