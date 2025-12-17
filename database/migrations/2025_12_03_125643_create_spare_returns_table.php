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
        Schema::create('spare_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->unsignedBigInteger('spare_id')->nullable();
            $table->string('barcode_no', 100)->nullable();
            $table->string('new_barcode_no', 100)->nullable();
            $table->text('code_html')->nullable();
            $table->text('code_base64_img')->nullable();
            $table->double('rate', 10, 2)->nullable();
            $table->unsignedBigInteger('repair_id')->nullable();
            $table->integer('crp_id')->nullable();
            $table->unsignedBigInteger('goods_id')->nullable();
            $table->integer('goods_supplier_warranty_period')->nullable();
            $table->tinyInteger('in_warranty')->default(0)->comment('is_item_supplier_warranty');
            $table->tinyInteger('is_returned')->default(0)->comment('is returned from s.p. , if done entry record to spare inv.');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('goods_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('spare_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('repair_id')->references('id')->on('repairs')->onDelete('cascade');
            $table->foreign('service_partner_id')->references('id')->on('service_partners')->onDelete('cascade');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_returns');
    }
};
