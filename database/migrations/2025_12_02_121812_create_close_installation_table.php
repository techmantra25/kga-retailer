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
        Schema::create('close_installation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('installation_id')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable()->comment('auth user id / master');
            $table->enum('qa1', ['yes', 'no'])->default('no')->comment('Have you called customer before close this call');
            $table->enum('qa2', ['yes', 'no'])->default('no')->comment('Is the installation completed successfully');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Foreign keys
            $table->foreign('installation_id')->references('id')->on('installations')->onDelete('cascade');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_installation');
    }
};
