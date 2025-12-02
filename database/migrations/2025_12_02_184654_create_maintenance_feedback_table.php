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
        Schema::create('maintenance_feedback', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('maintenance_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('customer_name', 250)->nullable();
            $table->string('customer_phone', 250)->nullable();
            $table->string('bill_no', 250)->nullable();
            $table->smallInteger('feedback')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexes
            $table->index('maintenance_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_feedback');
    }
};
