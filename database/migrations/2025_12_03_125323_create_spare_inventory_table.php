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
        Schema::create('spare_inventory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('spare_return_id')->nullable();
            $table->unsignedBigInteger('spare_id')->nullable();
            $table->string('barcode_no', 150)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable()->comment('returned_by');
            $table->unsignedBigInteger('goods_id')->nullable()->comment('repaired goods');
            $table->double('rate', 10, 2)->nullable();
            $table->tinyInteger('is_returned')->default(0)->comment('is return to supplier or not');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('spare_id');
            $table->index('service_partner_id');
            $table->index('goods_id');
            $table->index('spare_return_id');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_inventory');
    }
};
