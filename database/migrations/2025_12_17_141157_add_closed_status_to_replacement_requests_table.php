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
        Schema::table('replacement_requests', function () {
                 DB::statement("
                ALTER TABLE replacement_requests 
                MODIFY status ENUM(
                    'pending',
                    'report_uploaded',
                    'level_approval_1',
                    'completed',
                    'dispatched',
                    'closed'
                ) NOT NULL DEFAULT 'pending'
            ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replacement_requests', function () {
             DB::statement("
                ALTER TABLE replacement_requests 
                MODIFY status ENUM(
                    'pending',
                    'report_uploaded',
                    'level_approval_1',
                    'completed',
                    'dispatched'
                ) NOT NULL DEFAULT 'pending'
            ");
        });
    }
};
