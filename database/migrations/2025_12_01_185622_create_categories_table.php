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
          Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('product_type', ['fg', 'sp'])->nullable();
            $table->string('name', 100)->default('')->nullable();
            $table->tinyInteger('amc_applicable')->default(0)->comment('0:No,1:Yes');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('description')->nullable();
            $table->string('image', 100)->default('')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
    
            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
