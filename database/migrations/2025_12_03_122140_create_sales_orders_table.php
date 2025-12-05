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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no', 100)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('crp_id')->nullable();
            $table->integer('dap_id')->nullable();
            $table->double('order_amount', 10, 2)->default(0.00)->comment('order total amount')->nullable();
            $table->enum('type', ['fg', 'sp'])->default('sp')->nullable();
            $table->longText('details')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'ongoing'])->default('pending')->nullable();
            $table->string('address', 100)->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->text('note')->nullable()->comment('comments on order');
            $table->longText('signature')->nullable()->comment('e signature');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('customer_id');
            $table->index('dealer_id');
            $table->index('service_partner_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
