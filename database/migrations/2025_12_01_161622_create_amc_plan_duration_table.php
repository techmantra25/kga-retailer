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
        Schema::create('amc_plan_duration', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('amc_id')->nullable()->comment('From (amc_plan_type) table');
            $table->integer('duration')->default(0);
            $table->integer('deep_cleaning')->nullable();
            $table->integer('normal_cleaning')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate();

            // $table->foreign('amc_id')->references('id')->on('amc_plan_type')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amc_plan_duration');
    }
};
