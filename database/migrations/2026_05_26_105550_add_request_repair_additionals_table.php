<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            // Additional info saat transisi ke on_trial
            $table->string('penyebab_vc')->nullable()->after('detail_proyek');
            $table->enum('tindakan_repair', ['Pertama', 'Berulang'])->nullable()->after('penyebab_vc');

            // Status timestamps untuk hitung durasi & timeline
            $table->timestamp('on_process_at')->nullable()->after('tindakan_repair');
            $table->timestamp('on_trial_at')->nullable()->after('on_process_at');
            $table->timestamp('closed_at')->nullable()->after('on_trial_at');
        });
    }

    public function down(): void
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            $table->dropColumn([
                'penyebab_vc',
                'tindakan_repair',
                'on_process_at',
                'on_trial_at',
                'closed_at',
            ]);
        });
    }
};