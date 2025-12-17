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
        Schema::create('dap_discount_request', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('dap_id');
            $table->double('discount_amount', 10, 2)->default(0.00);
            $table->double('approval_amount', 10, 2)->default(0.00);
            $table->unsignedBigInteger('approval_by')->nullable()->comment('user id from users table');

            $table->tinyInteger('status');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            // $table->foreign('dap_id')->references('id')->on('dap_services')->onDelete('cascade');
            $table->foreign('approval_by')->references('id')->on('users')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_discount_request');
    }
};
