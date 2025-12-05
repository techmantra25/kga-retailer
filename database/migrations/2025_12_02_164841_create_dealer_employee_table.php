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
        Schema::create('dealer_employee', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('name', 255);
            $table->bigInteger('phone');
            $table->string('password', 255);
            $table->integer('dealer_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('dealer_type', 255)->nullable();
            $table->integer('status')->default(1)->comment('1:Active, 0:Inactive');
            $table->tinyInteger('from_where')->default(0)->comment('1:though dealer, 0:from employee')->nullable();
            $table->text('mac_address')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Index
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_employee');
    }
};
