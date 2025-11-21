<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_checkup_details', function (Blueprint $table) {
            $table->json('ng_action_data')->nullable()->after('ng_action_status');
        });
    }

    public function down(): void
    {
        Schema::table('history_checkup_details', function (Blueprint $table) {
            $table->dropColumn('ng_action_data');
        });
    }
};