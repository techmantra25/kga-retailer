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
        Schema::create('amc_plan_type', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedBigInteger('plan_asset_id')->nullable();
            $table->integer('deleted_at')->default(1)->comment('0:soft-delete');
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();

            // $table->foreign('plan_asset_id')->references('id')->on('plan_assets')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amc_plan_type');
    }
};
