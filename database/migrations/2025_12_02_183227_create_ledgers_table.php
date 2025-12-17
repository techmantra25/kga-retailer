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

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_partner_id')->references('id')->on('service_partners')->onDelete('cascade');
            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');
            // $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('installation_id')->references('id')->on('installations')->onDelete('cascade');
            $table->foreign('repair_id')->references('id')->on('repairs')->onDelete('cascade');
            $table->foreign('maintenance_id')->references('id')->on('maintenances')->onDelete('cascade');
            $table->foreign('credit_note_id')->references('id')->on('credit_note')->onDelete('cascade');

          
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
