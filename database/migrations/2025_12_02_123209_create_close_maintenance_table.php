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
        Schema::create('close_maintenance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('maintenance_id')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->tinyInteger('is_new_parts_required')->default(0);
            $table->longText('parts_description')->nullable();
            $table->enum('qa1', ['yes', 'no'])->comment('Have you called customer before close this call');
            $table->enum('qa2', ['yes', 'no'])->comment('Is the repair completed successfully');
            $table->longText('remarks')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Foreign Keys
            $table->foreign('closed_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('maintenance_id')
                ->references('id')
                ->on('maintenances')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_maintenance');
    }
};
