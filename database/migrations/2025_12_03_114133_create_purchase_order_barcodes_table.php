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
        Schema::create('purchase_order_barcodes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('product_id');
            $table->string('barcode_no', 100)->default('')->nullable();
            $table->longText('code_html')->nullable();
            $table->longText('code_base64_img')->nullable();

            $table->tinyInteger('is_scanned')->default(0);
            $table->tinyInteger('is_bulk_scanned')->default(0);
            $table->tinyInteger('is_stock_in')->default(0);
            $table->tinyInteger('is_archived')->default(0);

            $table->dateTime('archived_at')->nullable();
            $table->unsignedBigInteger('scanned_by')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('purchase_order_id');
            $table->index('product_id');
            $table->index('scanned_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_barcodes');
    }
};
