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
        Schema::create('repairs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->unsignedBigInteger('dealer_user_id')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();

            $table->string('unique_id', 100)->nullable();
            $table->string('service_partner_email', 255)->nullable();
            $table->string('dealer_user_name', 200)->nullable();
            $table->string('customer_name', 200)->nullable();
            $table->string('customer_phone', 12)->nullable();
            $table->string('pincode', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('bill_no', 250)->nullable();
            $table->date('order_date')->nullable();
            $table->string('product_value', 250)->nullable();
            $table->string('product_sl_no', 100)->nullable();
            $table->string('product_name', 250)->nullable();
            $table->unsignedBigInteger('product_id')->nullable();

            $table->string('warranty_status', 100)->nullable();
            $table->string('warranty_period', 100)->nullable();
            $table->date('warranty_date')->nullable()->comment('warranty valid from order date');
            $table->double('repair_charge', 10, 2)->nullable();
            $table->tinyInteger('in_warranty')->default(0);
            $table->tinyInteger('is_spare_added')->default(0);
            $table->double('service_charge', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('snapshot_file', 100)->nullable();
            $table->tinyInteger('is_closed')->default(0);
            $table->string('closing_otp', 10)->nullable();
            $table->dateTime('closing_otp_started_at')->nullable();
            $table->dateTime('closing_otp_expired_at')->nullable();
            $table->tinyInteger('is_cancelled')->default(0);
            $table->tinyInteger('is_repeated')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('dealer_user_id');
            $table->index('service_partner_id');
            $table->index('dealer_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
