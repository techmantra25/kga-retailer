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
        Schema::create('before_amc_subscription', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kga_sales_id')->comment('from kga_sales_data table');
            $table->string('amc_unique_number')->nullable();
            $table->integer('product_id');
            $table->string('serial');
            $table->integer('comprehensive_warranty')->nullable();
            $table->integer('amc_id')->comment('From products_amc table');
            $table->double('actual_amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->integer('discount_request')->default(0)->comment('for discount request(from API end)');
            $table->double('purchase_amount', 10, 2);
            $table->timestamp('payment_time')->nullable();
            $table->integer('status')->default(0)->comment('0:failed, 1:success ; 2:pending request; 3= admin approved');
            $table->string('bill_path')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            $table->text('notification_channels')->nullable();
            $table->enum('type', ['staff','servicepartner'])->nullable()
                ->comment('staff from user table , service_partner from service partner table');
            $table->integer('sell_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw("'0000-00-00 00:00:00'"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('before_amc_subscription');
    }
};
