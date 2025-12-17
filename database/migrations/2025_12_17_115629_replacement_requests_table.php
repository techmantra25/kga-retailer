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
        Schema::create('replacement_requests', function (Blueprint $table) {

            $table->bigIncrements('id');

            // 🔗 Foreign key
            $table->unsignedBigInteger('crp_id');

            $table->string('report_file')->nullable();
            $table->boolean('report_uploaded')->default(0);
            $table->dateTime('report_required_till')->nullable();

            $table->unsignedBigInteger('approval1_by')->nullable();
            $table->dateTime('approval1_at')->nullable();

            $table->unsignedBigInteger('approval2_by')->nullable();
            $table->dateTime('approval2_at')->nullable();

            $table->enum('status', [
                'pending',
                'report_uploaded',
                'level_approval_1',
                'completed',
                'dispatched'
            ])->default('pending');

            $table->text('remarks')->nullable();

            $table->timestamps();

            //  Foreign Key Constraint
            $table->foreign('crp_id')
                ->references('id')
                ->on('customer_point_services')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replacement_requests');
    }
};
