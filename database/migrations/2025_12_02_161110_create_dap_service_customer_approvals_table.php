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
        Schema::create('dap_service_customer_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dap_service_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('service_centre_id')->nullable();
            $table->string('item', 250)->nullable();
            $table->string('customer_name', 250)->nullable();
            $table->string('customer_mobile', 50)->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->enum('cust_calling_status', ['not_received', 'answered', 'declined', 'refuse_to_repair'])->nullable();
            $table->string('repairing_otp', 10)->nullable();
            $table->dateTime('repairing_otp_expired_at')->nullable();
            $table->tinyInteger('is_otp_validated')->default(0)->comment('Is OTP Validated By Customer');

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexes
            $table->index('dap_service_id');
            $table->index('product_id');
            $table->index('service_centre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_service_customer_approvals');
    }
};
