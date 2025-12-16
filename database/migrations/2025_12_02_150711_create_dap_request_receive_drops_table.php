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
        Schema::create('dap_request_receive_drops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('entry_date');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('challan_image', 250)->nullable();
            $table->string('unique_id', 150)->nullable();
            $table->text('dap_service_ids')->nullable();
            $table->double('amount', 10, 2);
            $table->unsignedBigInteger('service_centre_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_request_receive_drops');
    }
};
