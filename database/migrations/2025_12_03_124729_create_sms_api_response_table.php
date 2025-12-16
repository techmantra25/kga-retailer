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
        Schema::create('sms_api_response', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sms_template_id', 250)->nullable();
            $table->string('sms_entity_id', 250)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('message_body')->nullable();
            $table->text('response_body')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_api_response');
    }
};
