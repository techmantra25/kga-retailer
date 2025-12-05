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
        Schema::create('product_warranty', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('goods_id')->nullable();
            
            $table->enum('dealer_type', ['khosla', 'nonkhosla'])->nullable();
            $table->enum('warranty_type', ['comprehensive', 'parts', 'additional', 'cleaning'])->nullable();
            
            $table->string('additional_warranty_type', 250)->nullable()->comment('1:Parts Chargeable, 2: Service Chargeable');
            $table->integer('spear_id')->nullable()->comment('from product table: if warranty_type=parts');
            $table->integer('warranty_period')->nullable()->comment('in months');
            $table->integer('number_of_cleaning')->default(0)->comment('if warranty_type = cleaning')->nullable();
            $table->integer('number_of_deep_cleaning')->default(0)->comment('if warranty_type="deep-cleaning"');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('goods_id');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warranty');
    }
};
