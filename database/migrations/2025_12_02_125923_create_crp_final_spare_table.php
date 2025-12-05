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
        Schema::create('crp_final_spare', function (Blueprint $table) {
            $table->bigIncrements('id'); 

            $table->unsignedBigInteger('crp_id');
            $table->unsignedBigInteger('spare_id');

            $table->integer('qty')->default(1);
            $table->string('product_damage', 250)->nullable();
            $table->double('actual_price', 10, 2)->default(0.00);
            $table->double('selling_price', 10, 2)->default(0.00);
            $table->string('old_barcode', 250)->nullable();
            $table->string('new_barcode', 255)->nullable();
            $table->text('new_code_html')->nullable();
            $table->text('new_code_base64_img')->nullable();
            $table->integer('warranty_status')->default(0)->comment('1:Yes, 0:No');
            $table->integer('return_required')->default(1)->comment('0:NO, 1:Yes');

            $table->dateTime('created_at')->nullable();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crp_final_spare');
    }
};
