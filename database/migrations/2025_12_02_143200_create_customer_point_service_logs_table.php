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
        Schema::create('customer_point_service_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('service_id'); 
            $table->text('purpose')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->timestamp('updated_at')->useCurrent(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_point_service_logs');
    }
};
