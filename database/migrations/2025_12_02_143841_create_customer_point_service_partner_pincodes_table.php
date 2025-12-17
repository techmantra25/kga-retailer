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
        Schema::create('customer_point_service_partner_pincodes', function (Blueprint $table) {
             $table->bigIncrements('id');

            $table->unsignedBigInteger('service_partner_id')->nullable();
            $table->unsignedBigInteger('pincode_id')->nullable();
            $table->bigInteger('number')->nullable();

            $table->enum('product_type', ['general', 'chimney'])
                ->default('general')
                ->comment('goods type');

            $table->boolean('is_from_csv')->default(0);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // $table->foreign('service_partner_id')->references('id')->on('service_partners')->onDelete('cascade');
            // $table->foreign('pincode_id')->references('id')->on('pincodes')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_point_service_partner_pincodes');
    }
};
