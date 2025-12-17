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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); 
            $table->string('invoice_no', 100)->nullable();
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->unsignedBigInteger('packingslip_id')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->double('total_amount', 10, 2)->default(0.00)->nullable();
            $table->double('paid_amount', 10, 2)->default(0.00)->nullable();
            $table->longText('customer_details')->nullable();
            $table->longText('item_details')->nullable();
            $table->timestamps();

            // $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            // $table->foreign('packingslip_id')->references('id')->on('packingslips')->onDelete('cascade');
            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');
            $table->foreign('service_partner_id')->references('id')->on('service_partners')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
