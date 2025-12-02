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
        Schema::create('installations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->unsignedBigInteger('dealer_user_id')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->string('unique_id', 100)->nullable();
            $table->string('bill_no', 250)->nullable();
            $table->string('service_partner_email', 255)->nullable();
            $table->string('pincode', 100)->nullable();
            $table->tinyInteger('mail_send')->default(0);
            $table->string('branch', 200)->nullable();
            $table->date('entry_date')->nullable();
            $table->string('customer_name', 250)->nullable();
            $table->text('address')->nullable();
            $table->string('district', 250)->nullable();
            $table->string('mobile_no', 250)->nullable();
            $table->string('phone_no', 250)->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('brand', 250)->nullable();
            $table->string('class', 250)->nullable();
            $table->string('salesman', 250)->nullable();
            $table->string('salesman_mobile_no', 250)->nullable();
            $table->string('product_value', 250)->nullable();
            $table->string('product_sl_no', 100)->nullable();
            $table->string('product_name', 250)->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->double('service_charge', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('csv_file_name', 250)->nullable();
            $table->text('snapshot_file')->nullable()->comment('dealer app - uploaded image'); 
            $table->string('invoice_image', 255)->nullable()->comment('installation invoice image file upload');
            $table->tinyInteger('is_closed')->default(0);
            $table->string('closing_otp', 10)->nullable();
            $table->dateTime('closing_otp_started_at')->nullable();
            $table->dateTime('closing_otp_expired_at')->nullable();
            $table->tinyInteger('is_urgent')->default(0);
            $table->dateTime('set_urgent_at')->nullable();
            $table->tinyInteger('is_cancelled')->default(0);
            $table->tinyInteger('is_chimney_sms_sent')->default(0)->comment('goods_type:chimney,check not closed and send greeting sms to customer');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('service_partner_id')->references('id')->on('service_partners')->nullOnDelete();
            $table->foreign('dealer_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('dealer_id')->references('id')->on('dealers')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installations');
    }
};
