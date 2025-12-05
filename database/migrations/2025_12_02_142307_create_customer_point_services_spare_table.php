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
        Schema::create('customer_point_services_spare', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crp_id');
            $table->integer('sp_id');
            $table->string('sp_name', 255);
            $table->integer('quantity')->default(1);
            $table->double('mop', 10, 2)->comment('From Product table');
            $table->double('last_po_cost_price', 10, 2)->comment("From purchase order product table");
            $table->float('profit_percentage', 10, 2);
            $table->double('final_amount', 10, 2);
            $table->integer('generate_by');
            $table->integer('is_grn_generate')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_point_services_spare');
    }
};
