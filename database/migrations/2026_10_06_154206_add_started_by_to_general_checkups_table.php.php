<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang kalau kolom sudah ada di server
        if (Schema::hasColumn('general_checkups', 'started_by')) {
            return;
        }

        Schema::table('general_checkups', function (Blueprint $table) {
            $table->foreignId('started_by')
                ->nullable()
                ->after('mulai_perbaikan')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('general_checkups', 'started_by')) {
            return;
        }

        Schema::table('general_checkups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('started_by');
        });
    }
};
