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
        Schema::create('dap_request_return_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dap_request_return_id')->nullable();
            $table->unsignedBigInteger('dap_service_id')->nullable();
            $table->unsignedBigInteger('service_centre_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();

            $table->string('item', 250)->nullable();
            $table->string('barcode', 150)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('dap_request_return_id')->references('id')->on('dap_request_returns')->onDelete('cascade');
            // $table->foreign('dap_service_id')->references('id')->on('dap_services')->onDelete('cascade');
            // $table->foreign('service_centre_id')->references('id')->on('service_centers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_request_return_items');
    }
};
