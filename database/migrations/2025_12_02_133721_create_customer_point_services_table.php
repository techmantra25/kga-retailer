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
        Schema::create('customer_point_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unique_id', 150)->unique()->nullable();
            $table->date('entry_date')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->integer('dealer_id')->nullable();
            $table->string('dealer_type', 250)->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('alternate_no', 50);
            $table->text('address')->nullable();
            $table->string('pincode', 250)->nullable();
            $table->string('issue', 255);
            $table->text('remarks')->nullable();
            $table->string('item', 255)->nullable();
            $table->string('class_name', 255)->nullable();
            $table->double('price', 10, 2)->default(0.00)->nullable();
            $table->date('bill_date')->nullable();
            $table->string('bill_no', 255)->nullable();
            $table->string('barcode', 255)->nullable();
            $table->longText('code_html')->nullable()->comment('for barcode generator');
            $table->longText('code_base64_img')->nullable()->comment('for barcode generator');
            $table->string('serial', 255)->nullable();
            $table->integer('repeat_call')->default(0)->comment('if call booked in 30 days from previous call booked with same serial thn 1');
            $table->integer('repeat_crp_id')->nullable()->comment('if repeat_call == 1 , then repeat_crp_id, other wise NULL');
            $table->tinyInteger('is_cancelled')->default(0);
            $table->tinyInteger('is_closed')->default(0);
            $table->string('closing_otp', 250)->nullable();
            $table->dateTime('closing_otp_time')->nullable();
            $table->tinyInteger('verify_closing_otp')->default(0);
            $table->tinyInteger('is_paid')->default(0);
            $table->enum('payment_method', ['online','cash'])->nullable();
            $table->tinyInteger('in_warranty')->default(0);
            $table->tinyInteger('is_spare_required')->nullable();
            $table->double('repair_charge', 10, 2)->nullable()->comment('For customer show');
            $table->double('spare_charge', 10, 2)->nullable();
            $table->double('total_amount', 10, 2)->default(0.00);
            $table->double('discount_amount', 10, 2)->default(0.00);
            $table->double('final_amount', 10, 2)->default(0.00);
            $table->double('total_service_charge', 10, 2)->default(0.00)->comment('For service partner')->nullable();
            $table->tinyInteger('assign_service_perter_id')->nullable();
            $table->text('snapshot_file')->nullable();
            $table->integer('admin_approval')->default(0)->comment('0: default ; 1:Approved; 2:Rejected');
            $table->integer('approved_by')->nullable();
            $table->integer('status')->default(0)->comment('0:Pending,1: Generate Packing Slip for Service Partner, 2:Generate Invoice for Service Partner, 3:Start Repairing,4:Approval requested for call close, 5:Invoice Generated For Customer, 6:Peyment link Send and waiting for payment, 7:Payment Successfull, 8:Closed call, 9:Cancelled Call');
            $table->integer('cancelled_by')->nullable()->comment('1:Admin, 2:Service Partner');
            $table->text('cancelled_reason')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('send_otp', 250)->nullable();
            $table->dateTime('send_otp_time')->nullable();
            $table->integer('otp_verified')->default(0);
            $table->string('packing_slip', 250)->nullable();
            $table->unsignedBigInteger('sales_orders_id')->nullable();
            $table->integer('packing_slip_status')->default(0)->comment('0:yet to generate 1: generated');
            $table->string('service_partner_invoice', 250)->nullable();
            $table->integer('return_spare')->default(0)->comment('0:No, 1:Yes');
            $table->string('return_spare_order', 250)->nullable()->comment('return_spare transaction_id');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexes
            $table->index('product_id');
            $table->index('branch_id');
            $table->index('sales_orders_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_point_services');
    }
};
