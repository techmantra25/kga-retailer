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
            $table->unsignedBigInteger('sales_order_id');
            $table->unsignedBigInteger('packingslip_id');
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->double('total_amount', 10, 2)->default(0.00)->nullable();
            $table->double('paid_amount', 10, 2)->default(0.00)->nullable();
            $table->longText('customer_details')->nullable();
            $table->longText('item_details')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('sales_order_id', 'invoices_sales_order_id_foreign');
            $table->index('packingslip_id', 'invoices_packingslip_id_foreign');
            $table->index('dealer_id');
            $table->index('service_partner_id');
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
