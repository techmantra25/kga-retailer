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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['credit', 'debit'])->default('credit')->nullable();
            $table->double('amount', 10, 2)->default(0.00)->nullable();
            $table->date('entry_date')->nullable();
            $table->enum('user_type', ['staff','servicepartner','dealer','service_centre','ho_sale','admin'])->default('staff')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('installation_id')->nullable();
            $table->unsignedBigInteger('repair_id')->nullable();
            $table->integer('dap_id')->nullable();
            $table->integer('crp_id')->nullable();
            $table->integer('amc_id')->nullable()->comment('from amc_subscription table');
            $table->integer('kga_sales_id')->nullable()->comment('	when buy amc , from kga_sales_data table	');
            $table->unsignedBigInteger('maintenance_id')->nullable();
            $table->unsignedBigInteger('credit_note_id')->nullable();
            $table->string('purpose', 250)->nullable();
            $table->string('transaction_id', 250)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('service_partner_id');
            $table->index('dealer_id');
            $table->index('payment_id');
            $table->index('installation_id');
            $table->index('repair_id');
            $table->index('maintenance_id');
            $table->index('credit_note_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
