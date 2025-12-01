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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id(); // id
            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('mac_address')->nullable();
            $table->string('password');
            $table->enum('dealer_type', ['khosla', 'nonkhosla'])->nullable(); // enum type, default NULL
            $table->text('address')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('license_no')->nullable()->comment('Trade license number'); // license number
            $table->string('pincode')->nullable();
            $table->tinyInteger('status')->default(1); // status
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};
