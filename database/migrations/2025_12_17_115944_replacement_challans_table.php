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
         Schema::create('replacement_challans', function (Blueprint $table) {

            $table->increments('id');

            // Foreign key column
            $table->unsignedBigInteger('replacement_request_id');

            $table->string('challan_no');
            $table->text('customer_details')->nullable();
            $table->text('product_details')->nullable();

            $table->date('created_at')->nullable();
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate();

            //  Foreign Key Constraint
            $table->foreign('replacement_request_id', 'replacement_req_id_fk')
                ->references('id')
                ->on('replacement_requests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replacement_challans');
    }
};
