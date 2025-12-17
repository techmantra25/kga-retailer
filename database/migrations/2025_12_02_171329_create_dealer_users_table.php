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
        Schema::create('dealer_users', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');
            $table->string('name', 200)->nullable();
            $table->string('company_name', 250)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('mac_address', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_users');
    }
};
