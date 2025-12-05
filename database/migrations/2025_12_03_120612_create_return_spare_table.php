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
        Schema::create('return_spare', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('dealer_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->integer('crp_id')->nullable()->comment('customer_point_services table');
            $table->string('transaction_id', 150)->nullable();
            $table->string('grn_no', 250)->nullable();
            $table->double('amount', 10, 2)->nullable();
            $table->tinyInteger('is_goods_in')->default(0);
            $table->enum('goods_in_type', ['bulk', 'scan'])->default('scan');
            $table->tinyInteger('status')->default(1)->comment('1:Pending;2:Received;3:Cancelled');

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_spare');
    }
};
