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
        Schema::create('return_spare_barcodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('return_spare_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('barcode_no', 100)->nullable();
            $table->longText('code_html')->nullable();
            $table->longText('code_base64_img')->nullable();
            $table->tinyInteger('is_scanned')->default(0);
            $table->tinyInteger('is_bulk_scanned')->default(0);
            $table->tinyInteger('is_stock_in')->default(0);
            $table->unsignedBigInteger('scanned_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('return_spare_id')->references('id')->on('return_spare')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('scanned_by')->references('id')->on('users')->onDelete('cascade');

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_spare_barcodes');
    }
};
