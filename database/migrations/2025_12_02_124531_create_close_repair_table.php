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
        Schema::create('close_repair', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('repair_id')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();

            $table->tinyInteger('is_new_parts_required')->default(0);
            $table->text('parts_description')->nullable();
            $table->enum('qa1', ['yes', 'no'])->default('no')
                ->comment('Have you called customer before close this call');
            $table->enum('qa2', ['yes', 'no'])->default('no')
                ->comment('Is the repair completed successfully');
            $table->text('remarks')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Foreign keys
            $table->foreign('repair_id')
                ->references('id')->on('repairs')
                ->cascadeOnDelete();

            $table->foreign('closed_by')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_repair');
    }
};
