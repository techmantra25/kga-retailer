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
        Schema::create('amc_subscription', function (Blueprint $table) {
            $table->bigInteger('id', false, true)->autoIncrement(); 
            $table->integer('kga_sales_id')->comment('from kga_sales_data table');
            $table->string('amc_unique_number', 255)->nullable();
            $table->integer('product_id');
            $table->string('serial', 255);
            $table->integer('comprehensive_warranty')->nullable();
            $table->date('comprehensive_warranty_end_date');
            $table->integer('amc_id')->comment('From products_amc table');
            $table->date('purchase_date');

            $table->double('actual_amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->double('purchase_amount', 10, 2);

            $table->date('amc_start_date');
            $table->date('amc_end_date');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw("'0000-00-00 00:00:00'"));

            $table->enum('type', ['staff', 'servicepartner'])->default('staff');
            $table->integer('sell_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amc_subscription');
    }
};
