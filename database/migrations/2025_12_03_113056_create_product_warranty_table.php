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

            $table->foreign('goods_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

           
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
