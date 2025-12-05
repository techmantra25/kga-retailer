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
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('csv_to_email', 255)->nullable()->comment('for csv, master to email');
            $table->string('whatsapp_instance_id', 250)->nullable();
            $table->string('sms_template_id', 250)->default('1707168847759989501')->nullable();
            $table->string('sms_entity_id', 250)->default('1701159671476365690')->nullable();
            $table->string('cf_app_id_test', 255)->nullable();
            $table->string('cf_secret_key_test', 255)->nullable();
            $table->string('cf_app_id_live', 255)->nullable();
            $table->string('cf_secret_key_live', 255)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
