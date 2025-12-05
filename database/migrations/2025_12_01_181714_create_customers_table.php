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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->nullable()->default('');
            $table->string('email', 100)->nullable()->default('');
            $table->string('phone', 100)->nullable()->default('');
            $table->string('address', 250)->nullable();
            $table->string('pincode', 100)->nullable();
            $table->string('gst_no', 200)->nullable();
            $table->string('pan_no', 200)->nullable();
            $table->string('license_no', 250)->nullable()->comment('trade license number');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
