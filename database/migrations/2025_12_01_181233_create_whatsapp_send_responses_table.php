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
       Schema::create('whatsapp_send_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mobile', 20);
            $table->string('template_name', 100)->nullable();
            $table->longText('request_json')->nullable();
            $table->longText('response_json')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_send_responses');
    }
};
