<?php
// database/migrations/2024_12_23_add_active_request_to_parts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->foreignId('active_request_id')
                  ->nullable()
                  ->after('id_pud')
                  ->constrained('part_requests')
                  ->nullOnDelete();
            
            $table->index('active_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign(['active_request_id']);
            $table->dropColumn('active_request_id');
        });
    }
};