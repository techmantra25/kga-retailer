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
        Schema::create('dap_request_returns', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('unique_id', 150);
            $table->date('entry_date');

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('service_centre_id')->nullable();

            $table->string('challan_image', 250)->nullable();
            $table->double('amount', 10, 2)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexes
            $table->index('branch_id');
            $table->index('service_centre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_request_returns');
    }
};
