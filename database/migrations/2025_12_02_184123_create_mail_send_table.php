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
        Schema::create('mail_send', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            $table->enum('mail_for', ['installation', 'repair'])->default('installation');
            $table->string('bill_no')->nullable();
            $table->longText('details')->nullable();
            $table->boolean('is_attachment')->default(0);
            $table->longText('attachement_files')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_send');
    }
};
