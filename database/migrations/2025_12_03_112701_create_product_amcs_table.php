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
        Schema::create('product_amcs', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->integer('plan_id')->comment('1: cleaning, 2:cleaning & repairing, 3:cleaning & repairing & spare');
            $table->integer('duration_id')->comment('From (amc_plan_duration) table');
            $table->integer('duration')->comment('In days');
            $table->double('amount', 10, 2)->default(0.00);
            $table->string('month_val', 255)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('product_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_amcs');
    }
};
