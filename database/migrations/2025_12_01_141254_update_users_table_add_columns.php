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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('name');
            $table->text('mac_address')->nullable()->comment('mobile app device id')->after('phone');
            $table->enum('type', ['admin', 'manager', 'staff'])->default('manager')->after('mac_address');
            $table->unsignedBigInteger('role_id')->nullable()->after('type');
            $table->unsignedBigInteger('branch_id')->nullable()->after('role_id');
            $table->tinyInteger('status')->default(1)->after('branch_id');
            $table->double('amc_incentive', 10, 2)->default(0.00)->after('status');

            // Add foreign key constraints
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             // Drop foreign keys first
            $table->dropForeign(['role_id']);
            $table->dropForeign(['branch_id']);

            // Drop the columns
            $table->dropColumn([
                'phone',
                'mac_address',
                'type',
                'role_id',
                'branch_id',
                'status',
                'amc_incentive',
            ]);
        });
    }
};
