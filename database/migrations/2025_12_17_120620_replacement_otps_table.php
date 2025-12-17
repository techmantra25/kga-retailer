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
        Schema::create('replacement_otps', function (Blueprint $table) {
            $table->integer('id', true); 

            $table->bigInteger('replacement_request_id')->unsigned()->nullable();

            $table->string('otp', 255);

            $table->date('created_at')->nullable();

            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate();

             $table->foreign('replacement_request_id')->references('id')->on('replacement_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replacement_otps');
    }
};
