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
        Schema::create('dap_request_receives', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('dap_request_receive_drop_id')->nullable();
            $table->unsignedBigInteger('dap_service_id')->nullable();
            $table->unsignedBigInteger('service_centre_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();

            $table->string('item', 250)->nullable();
            $table->string('barcode', 150)->nullable();
            $table->longText('code_html')->nullable();
            $table->longText('code_base64_img')->nullable();

            $table->tinyInteger('is_scanned')->default(0);
            $table->tinyInteger('is_service_centre_received')->default(0);
            $table->tinyInteger('is_repaired')->default(0)->comment('is the item repaired completely');
            $table->tinyInteger('is_done')->default(0)->comment('done due to repaired or customer refuse to repair');
            $table->tinyInteger('is_returned')->default(0)->comment('is the item returned to branch');

            $table->text('remarks')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            // Indexes
            $table->index('dap_service_id');
            $table->index('service_centre_id');
            $table->index('product_id');
            $table->index('dap_request_receive_drop_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_request_receives');
    }
};
