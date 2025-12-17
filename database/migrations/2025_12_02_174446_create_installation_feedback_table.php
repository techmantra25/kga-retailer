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
        Schema::create('installation_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installation_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('customer_name', 250)->nullable();
            $table->string('customer_phone', 250)->nullable();
            $table->string('bill_no', 250)->nullable();
            $table->smallInteger('feedback')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('installation_id')->references('id')->on('installations')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
         

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installation_feedback');
    }
};
