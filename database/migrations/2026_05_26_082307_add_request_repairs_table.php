<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            $table->enum('status', ['on_process', 'on_trial', 'closed'])
                  ->default('on_process')
                  ->after('detail_proyek');
        });
    }

    public function down(): void
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};