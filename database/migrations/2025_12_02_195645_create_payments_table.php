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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); 
            $table->enum('user_type', ['staff', 'servicepartner', 'dealer', 'ho_sale'])
                  ->default('servicepartner')->nullable();
            $table->unsignedBigInteger('ho_sale_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->date('entry_date')->nullable();
            $table->string('voucher_no', 150)->nullable();
            $table->double('amount', 10, 2)->nullable();
            $table->enum('payment_mode', ['cash', 'cheque', 'neft'])->default('cash')->nullable();
            $table->string('chq_utr_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('narration')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexes
            $table->index('service_partner_id');
            $table->index('created_by');
            $table->index('ho_sale_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
