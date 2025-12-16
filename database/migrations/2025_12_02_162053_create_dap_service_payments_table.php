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
        Schema::create('dap_service_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dap_service_id')->nullable();
            $table->string('payment_id', 250)->nullable();
            $table->string('invoice_id', 255)->nullable();
            $table->double('amount', 10, 2)->nullable();
            $table->string('cashfree_customer_id', 250)->nullable();
            $table->string('cashfree_order_id', 250)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('customer_name', 250)->nullable();
            $table->string('customer_phone', 250)->nullable();
            $table->string('status', 250)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Index
            $table->index('dap_service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_service_payments');
    }
};
