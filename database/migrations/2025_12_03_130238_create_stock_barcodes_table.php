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
        Schema::create('stock_barcodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('packingslip_id')->nullable()->comment('for scan goods out');
            $table->string('barcode_no', 100)->default('')->nullable();
            $table->longText('code_html')->nullable();
            $table->longText('code_base64_img')->nullable();
            $table->integer('is_damage')->default(0)->comment('0:good , 1:DEFECTIVE');
            $table->tinyInteger('is_scanned')->default(0);
            $table->tinyInteger('is_bulk_scanned')->default(0);
            $table->tinyInteger('is_stock_out')->default(0);
            $table->unsignedBigInteger('scanned_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('stock_id');
            $table->index('product_id');
            $table->index('packingslip_id');
            $table->index('scanned_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_barcodes');
    }
};
