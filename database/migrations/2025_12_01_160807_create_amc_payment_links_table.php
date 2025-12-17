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
        Schema::create('amc_payment_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kga_sales_id')->nullable();
            $table->string('amc_unique_number', 255)->nullable();
            $table->text('link')->nullable();
            $table->integer('status')->default(0)->comment('0:failed, 1:paid');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate();

            // $table->foreign('kga_sale_id')->references('id')->on('kga_sales_data')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amc_payment_links');
    }
};
