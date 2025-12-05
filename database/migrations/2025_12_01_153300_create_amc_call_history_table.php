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
        Schema::create('amc_call_history', function (Blueprint $table) {
            $table->id();
            $table->integer('kga_sale_id')->comment('table from KGA_SALES_DATA table');
            $table->integer('type')->default(1)->comment('1: call back, 2: refused');
            $table->integer('reminder_days')->nullable();
            $table->date('reminder_date')->nullable();
            $table->string('remarks', 255)->nullable();
            $table->integer('auth_id')->nullable();
            $table->text('ip')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amc_call_history');
    }
};
