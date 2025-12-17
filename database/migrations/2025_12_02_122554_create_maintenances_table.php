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
        Schema::create('maintenances', function (Blueprint $table) {
              $table->bigIncrements('id');

            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();

            $table->string('unique_id', 100)->nullable();
            $table->string('pincode', 100)->nullable();
            $table->string('bill_no', 250)->nullable();

            $table->date('order_date');
            $table->string('customer_name', 200)->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->longText('address')->nullable();

            $table->string('product_value', 250)->nullable();
            $table->string('product_sl_no', 250)->nullable();

            $table->integer('repeat_call')->default(0)->comment('if call generate in 30 days then 1');
            $table->integer('repeat_id')->nullable();

            $table->string('product_name', 250)->nullable();

            $table->enum('service_for', ['chimney', 'motor'])->default('chimney');
            $table->enum('service_type', ['cleaning', 'repairing', 'deep_cleaning'])->default('repairing');
            $table->enum('maintenance_type', ['free', 'additional', 'amc', 'out_of_warranty'])->nullable();

            $table->tinyInteger('is_spare_chargeable')->default(0);
            $table->tinyInteger('is_repair_chargeable')->default(0);
            $table->tinyInteger('out_of_warranty')->default(0);
            $table->tinyInteger('is_closed')->default(0);
            $table->tinyInteger('is_amc')->default(0);

            $table->string('closing_otp', 10)->nullable();
            $table->dateTime('closing_otp_started_at')->nullable();
            $table->dateTime('closing_otp_expired_at')->nullable();

            $table->longText('remarks')->nullable();
            $table->double('service_charge', 10, 2)->nullable()->comment('service partner repair charge');

            $table->tinyInteger('is_spare_added')->default(0);
            $table->tinyInteger('is_cancelled')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');
            $table->foreign('service_partner_id')->references('id')->on('service_partners')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
