<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            $table->string('status_after_trial', 2)->nullable()->after('tindakan_repair'); // OK / NG
            $table->text('point_verifikasi')->nullable()->after('status_after_trial');
            $table->string('approval_section_chief', 255)->nullable()->after('point_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            $table->dropColumn(['status_after_trial', 'point_verifikasi', 'approval_section_chief']);
        });
    }
};