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
        Schema::create('credit_note', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->enum('user_type', ['service_partner', 'ho_sale', '', ''])->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->enum('call_type', ['installation','repair','amc'])->nullable();
            $table->string('call_no', 150)->nullable();
            $table->unsignedBigInteger('ho_sale_id')->nullable();
            $table->unsignedBigInteger('amc_id')->nullable();
            $table->string('amc_unique_number', 255)->nullable();
            $table->unsignedBigInteger('installation_id')->nullable();
            $table->unsignedBigInteger('repair_id')->nullable();
            $table->string('transaction_id', 150)->nullable();
            $table->date('entry_date')->nullable();
            $table->double('amount', 10, 2)->default(0.00);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('service_partner_id')->references('id')->on('service_partners')->onDelete('cascade');
            $table->foreign('installation_id')->references('id')->on('installations')->onDelete('cascade');
            $table->foreign('repair_id')->references('id')->on('repairs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ho_sale_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('amc_id')->references('id')->on('amc_subscription')->onDelete('cascade');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_note');
    }
};
