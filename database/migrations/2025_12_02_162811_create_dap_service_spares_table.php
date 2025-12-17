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
        Schema::create('dap_service_spares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dap_service_id')->nullable();
            $table->unsignedBigInteger('spare_id')->nullable();
            $table->integer('quantity')->default(0);
            $table->double('cost_price', 10, 2)->nullable();
            $table->string('profit_percentage', 20)->nullable();
            $table->double('spare_profit_val', 10, 2)->nullable();
            $table->double('total_spare_charge', 10, 2)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('dap_service_id')->references('id')->on('dap_services')->onDelete('cascade');
            $table->foreign('spare_id')->references('id')->on('products')->onDelete('cascade');
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_service_spares');
    }
};
