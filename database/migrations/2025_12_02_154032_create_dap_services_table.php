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
        Schema::create('dap_services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('unique_id', 150)->nullable();
            $table->date('entry_date')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            $table->string('customer_name', 255)->nullable();
            $table->bigInteger('mobile')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('address', 250)->nullable();
            $table->bigInteger('alternate_no');

            $table->string('issue', 255);
            $table->string('item', 255)->nullable();
            $table->string('class_name', 255)->nullable();

            $table->date('bill_date')->nullable();
            $table->string('bill_no', 255)->nullable();
            $table->string('barcode', 255)->nullable();
            $table->longText('code_html')->nullable()->comment('for barcode generator');
            $table->longText('code_base64_img')->nullable()->comment('for barcode generator');
            $table->string('serial', 255)->nullable();

            $table->integer('repeat_call')->default(0)->comment('if call booked in 30 days from previous call booked with same serial thn 1');
            $table->integer('repeat_dap_id')->nullable()->comment('if repeat_call == 1 , then repeat_dap_id, other wise NULL');

            $table->tinyInteger('is_cancelled')->default(0);
            $table->tinyInteger('is_closed')->default(0);
            $table->integer('quotation_status')->default(0)->comment('1=quotation generate');

            $table->string('send_otp', 250)->nullable();
            $table->dateTime('send_otp_time')->nullable();
            $table->integer('otp_verified')->default(0)->comment('0:pending, 1:verified');

            $table->string('delivery_otp', 5);
            $table->dateTime('delivery_otp_time');
            $table->integer('verify_delivery_otp')->default(0);

            $table->dateTime('customer_delivery_time')->nullable();
            $table->tinyInteger('is_paid')->default(0)->comment('1:Paid, 2:Pending');
            $table->dateTime('payment_date');
            $table->enum('payment_method', ['online', 'cash'])->nullable();

            $table->tinyInteger('in_warranty')->default(0);
            $table->tinyInteger('in_motor_warranty')->nullable();
            $table->tinyInteger('is_spare_required')->default(0);

            $table->double('repair_charge', 10, 2)->nullable();
            $table->double('spare_charge', 10, 2)->nullable();
            $table->string('spear_part_qty', 250)->nullable();

            $table->double('total_amount', 10, 2)->default(0.00);
            $table->double('discount_amount', 10, 2)->default(0.00);
            $table->double('final_amount', 10, 2)->default(0.00)->comment('Excluded service charge & discount amount');
            $table->double('total_service_charge', 10, 2)->default(0.00)->nullable();

            $table->tinyInteger('is_dispatched_from_branch')->default(0)->comment('0:Yet to ditch patch, 1: Dispatched');
            $table->dateTime('is_dispatched_from_branch_date');

            $table->integer('is_received_at_branch')->default(0)->comment('0:unrecived, 1:received');
            $table->dateTime('is_received_at_branch_date');

            $table->tinyInteger('is_reached_service_centre')->default(0);
            $table->dateTime('is_reached_service_centre_date')->nullable();

            $table->integer('service_centre_dispatch')->default(0)->comment('0:not dispatched from service centre, 1: dispatched from service centre');
            $table->dateTime('service_centre_dispatch_date')->nullable();

            $table->tinyInteger('assign_service_perter_id')->nullable();
            $table->tinyInteger('wearhouse_id')->nullable();
            $table->string('vehicle_number', 255)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('employee_id')->nullable()->comment('from dealer_employees table');

            $table->unsignedBigInteger('dispatch_by')->nullable();
            $table->unsignedtinyInteger('return_road_challan')->default(0)->nullable();

            $table->string('return_type', 255)->nullable();
            $table->unsignedBigInteger('return_branch_id')->nullable();

            $table->string('return_vehicle_number', 255)->nullable();
            $table->text('return_transport_file')->nullable();

            $table->integer('sales_orders_id')->nullable();
            $table->string('packing_slip', 255)->nullable();
            $table->integer('packing_slip_status')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('employee_id')->references('id')->on('dealer_employee')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_services');
    }
};
