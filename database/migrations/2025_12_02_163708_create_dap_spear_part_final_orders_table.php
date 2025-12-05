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
        Schema::create('dap_spear_part_final_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dap_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('title', 255)->nullable();
            $table->double('price', 10, 2)->default(0.00);
            $table->string('profit_percentage', 250);
            $table->double('final_amount', 10, 2)->default(0.00);
            $table->integer('qry')->default(1);
            $table->integer('warranty_status')->default(0)->comment('0:False, 1: True');
            $table->string('old_spare_part_barcode', 250)->nullable();
            $table->string('new_spare_barcode', 250)->nullable();
            $table->longText('code_html')->nullable();
            $table->longText('code_base64_img')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexes
            $table->index('product_id', 'package_id_1');
            $table->index('dap_id', 'dap_id_k1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_spear_part_final_orders');
    }
};
