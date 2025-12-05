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
        Schema::create('service_partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name', 100)->nullable();
            $table->string('person_name', 255)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100)->unique()->nullable();
            $table->string('mac_address', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->text('about')->nullable();
            $table->string('photo', 250)->nullable();
            $table->text('address')->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pin', 100)->nullable();
            $table->string('aadhaar_no', 100)->nullable();
            $table->string('pan_no', 100)->nullable();
            $table->string('gst_no', 100)->nullable();
            $table->string('license_no', 100)->nullable();
            $table->double('salary', 10, 2)->default(0.00)->nullable();
            $table->double('repair_charge', 10, 2)->default(0.00)->nullable()->comment('incentive per repair as per product');
            $table->double('travelling_allowance', 10, 2)->default(0.00)->nullable();
            $table->tinyInteger('type')->default(1)->comment('1:24*7; 2:inhouse_technician; 3:local_vendors');
            $table->integer('service_partner_head')->nullable();
            $table->double('amc_incentive', 10, 2)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('is_from_csv')->default(0);
            $table->tinyInteger('is_default')->default(0)->comment('def.mail manged frm settings;csv_to_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_partners');
    }
};
