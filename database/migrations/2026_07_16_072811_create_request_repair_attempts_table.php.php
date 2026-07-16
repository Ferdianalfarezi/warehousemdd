<?php
// database/migrations/2026_07_16_000001_create_request_repair_attempts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_repair_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_repair_id')->nullable()->index();
            $table->string('no', 50);
            $table->integer('attempt_number')->default(1);
            $table->string('part_no', 100)->nullable();
            $table->string('nama')->nullable();
            $table->string('pic_names')->nullable();

            $table->string('analisa_penyebab')->nullable();
            $table->string('tindakan_perbaikan')->nullable();
            $table->string('catatan_penggantian_sparepart')->nullable();
            $table->string('item')->nullable();
            $table->string('proses_grinding')->nullable();
            $table->string('shim_up')->nullable();
            $table->string('status_burry', 10)->nullable();
            $table->string('standart_burry', 10)->nullable();
            $table->string('group_leader')->nullable();
            $table->string('operator')->nullable();
            $table->string('plan')->nullable();
            $table->string('actual')->nullable();
            $table->string('remark')->nullable();
            $table->string('judge', 10)->nullable();

            $table->date('tanggal_cek')->nullable();
            $table->string('lot_prod')->nullable();
            $table->string('awal', 10)->nullable();
            $table->string('tengah', 10)->nullable();
            $table->string('akhir', 10)->nullable();
            $table->string('qty', 10)->nullable();
            $table->string('remark_monitoring')->nullable();
            $table->string('judge_monitoring', 10)->nullable();
            $table->string('plan_permanen')->nullable();
            $table->string('actual_permanen')->nullable();
            $table->string('rootcause')->nullable();
            $table->string('recovery')->nullable();
            $table->string('assy_trial_check')->nullable();
            $table->string('judge_permanen', 10)->nullable();

            $table->dateTime('on_process_at')->nullable();
            $table->dateTime('on_trial_at')->nullable();
            $table->dateTime('ng_judged_at')->nullable();
            $table->integer('durasi_on_process_seconds')->nullable();
            $table->integer('durasi_on_trial_seconds')->nullable();
            $table->unsignedBigInteger('judged_by')->nullable();

            $table->timestamps();
        });

        Schema::table('request_repairs', function (Blueprint $table) {
            $table->integer('ng_attempt_count')->default(0)->after('judge');
        });

        Schema::table('request_repair_histories', function (Blueprint $table) {
            $table->integer('ng_attempt_count')->default(0)->after('judge');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_repair_attempts');
        Schema::table('request_repairs', fn (Blueprint $t) => $t->dropColumn('ng_attempt_count'));
        Schema::table('request_repair_histories', fn (Blueprint $t) => $t->dropColumn('ng_attempt_count'));
    }
};