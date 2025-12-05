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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cat_id')->nullable();
            $table->unsignedBigInteger('subcat_id')->nullable();
            $table->string('title', 250)->nullable();
            $table->string('unique_id', 100)->nullable()->default('');
            $table->text('description')->nullable();
            $table->string('public_name', 250)->nullable()->default('');
            $table->tinyInteger('is_title_public_name_same')->default(0);
            $table->double('mop', 10, 2)->nullable()->comment('Market Operating Price');
            $table->double('last_po_cost_price', 10, 2)->nullable();
            $table->double('repair_charge', 10, 2)->nullable()->comment('out of warranty goods repair charge payable for customer');
            $table->string('image', 200)->nullable();
            $table->integer('set_of_pcs')->nullable()->default(1);
            $table->string('warranty_status', 100)->nullable();
            $table->string('warranty_period', 100)->nullable();
            $table->enum('service_level', ['dealer', 'customer'])->nullable();
            $table->tinyInteger('is_installable')->default(0)->comment('0 1');
            $table->double('installable_amount', 10, 2)->default(0.00);
            $table->tinyInteger('is_amc_applicable')->default(0)->comment('0 1');
            $table->string('gst', 100)->nullable();
            $table->string('hsn_code', 100)->nullable();
            $table->enum('type', ['fg', 'sp'])->default('fg')->comment('fg: finished goods; sp: spare parts');
            $table->integer('comprehensive_warranty')->nullable()->comment('warranty period in months');
            $table->integer('comprehensive_warranty_free_services')->nullable();
            $table->integer('extra_warranty')->nullable()->comment('warranty period in months');
            $table->integer('motor_warranty')->nullable()->comment('warranty period in months (chimney specially)');
            $table->tinyInteger('status')->default(1);
            $table->enum('spare_type', ['general', 'motor'])->nullable();
            $table->enum('goods_type', ['general', 'chimney', 'gas_stove', 'ac', 'gieger'])->nullable();
            $table->string('profit_percentage', 50)->nullable()->comment('spare profit percentage');
            $table->integer('supplier_warranty_period')->nullable()->comment('(in months), for goods');
            $table->tinyInteger('is_test_product')->default(0)->comment('test product or not');

            $table->timestamps();

            $table->foreign('cat_id')->references('id')->on('categories')->onDelete('cascade');

            // $table->foreign('subcat_id')->references('id')->on('categories')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
