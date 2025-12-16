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

            // Indexes
            $table->index('dap_id', 'dap_id_1');
            $table->index('approval_by', 'approval_by_1');
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
