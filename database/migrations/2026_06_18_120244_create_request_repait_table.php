<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ════════════════════════════════════════════════════
        // TABLE: request_repairs
        // ════════════════════════════════════════════════════
        Schema::create('request_repairs', function (Blueprint $table) {
            $table->id();

            // ── Base Info ──
            $table->string('no')->unique();
            $table->date('tanggal_pengajuan')->nullable();
            $table->enum('group', ['A', 'B'])->nullable();
            $table->enum('shift', ['Pagi', 'Siang', 'Malam'])->nullable();
            $table->integer('jumlah_stroke')->default(0);
            $table->string('line_mesin')->nullable();

            // ── Barang ──
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('part_no')->nullable();
            $table->string('nama')->nullable();
            $table->string('process_no')->nullable();
            $table->string('customer')->nullable();

            // ── Jenis & Kategori ──
            $table->enum('jenis', ['Milik Sendiri', 'Eksternal'])->nullable();
            $table->date('target_selesai')->nullable();
            $table->enum('kategori_problem', ['Dies', 'Burry', 'Dimensi', 'Human Error', 'Accessories'])->nullable();
            $table->text('detail_proyek')->nullable();

            // ── Status & Timestamps ──
            $table->enum('status', ['on_process', 'on_trial', 'closed'])->default('on_process');
            $table->timestamp('on_process_at')->nullable();
            $table->timestamp('on_trial_at')->nullable();

            // ── Section 1: Tindakan Perbaikan (on_trial) ──
            $table->string('analisa_penyebab')->nullable();
            $table->string('tindakan_perbaikan')->nullable();
            $table->string('catatan_penggantian_sparepart')->nullable();

            // ── Section 2: Penanganan Problem Burry (on_trial) ──
            $table->string('item')->nullable();
            $table->string('proses_grinding')->nullable();
            $table->string('shim_up')->nullable();
            $table->enum('status_burry', ['OK', 'NG'])->nullable();
            $table->enum('standart_burry', ['OK', 'NG'])->nullable();
            $table->string('group_leader')->nullable();
            $table->string('operator')->nullable();

            // ── Section 3: Target Trial After Repair (on_trial) ──
            $table->string('plan')->nullable();
            $table->string('actual')->nullable();
            $table->string('remark')->nullable();
            $table->enum('judge', ['OK', 'NG'])->nullable();

            $table->timestamps();
        });

        // ════════════════════════════════════════════════════
        // TABLE: request_repair_histories
        // ════════════════════════════════════════════════════
        Schema::create('request_repair_histories', function (Blueprint $table) {
            $table->id();

            // ── Base Info ──
            $table->string('no')->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            $table->enum('group', ['A', 'B'])->nullable();
            $table->enum('shift', ['Pagi', 'Siang', 'Malam'])->nullable();
            $table->integer('jumlah_stroke')->default(0);
            $table->string('line_mesin')->nullable();

            // ── Barang ──
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('part_no')->nullable();
            $table->string('nama')->nullable();
            $table->string('process_no')->nullable();
            $table->string('customer')->nullable();

            // ── Jenis & Kategori ──
            $table->enum('jenis', ['Milik Sendiri', 'Eksternal'])->nullable();
            $table->date('target_selesai')->nullable();
            $table->enum('kategori_problem', ['Dies', 'Burry', 'Dimensi', 'Human Error', 'Accessories'])->nullable();
            $table->text('detail_proyek')->nullable();

            // ── Timestamps status ──
            $table->timestamp('on_process_at')->nullable();
            $table->timestamp('on_trial_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // ── Section 1: Tindakan Perbaikan (on_trial) ──
            $table->string('analisa_penyebab')->nullable();
            $table->string('tindakan_perbaikan')->nullable();
            $table->string('catatan_penggantian_sparepart')->nullable();

            // ── Section 2: Penanganan Problem Burry (on_trial) ──
            $table->string('item')->nullable();
            $table->string('proses_grinding')->nullable();
            $table->string('shim_up')->nullable();
            $table->enum('status_burry', ['OK', 'NG'])->nullable();
            $table->enum('standart_burry', ['OK', 'NG'])->nullable();
            $table->string('group_leader')->nullable();
            $table->string('operator')->nullable();

            // ── Section 3: Target Trial After Repair (on_trial) ──
            $table->string('plan')->nullable();
            $table->string('actual')->nullable();
            $table->string('remark')->nullable();
            $table->enum('judge', ['OK', 'NG'])->nullable();

            // ── Section 4: Monitoring Dies Temporary (closed) ──
            $table->date('tanggal_cek')->nullable();
            $table->string('lot_prod')->nullable();
            $table->enum('awal', ['OK', 'NG'])->nullable();
            $table->enum('tengah', ['OK', 'NG'])->nullable();
            $table->enum('akhir', ['OK', 'NG'])->nullable();
            $table->enum('qty', ['OK', 'NG'])->nullable();
            $table->string('remark_monitoring')->nullable();
            $table->enum('judge_monitoring', ['OK', 'NG'])->nullable();

            // ── Section 5: Target Permanen Action (closed) ──
            $table->string('plan_permanen')->nullable();
            $table->string('actual_permanen')->nullable();
            $table->string('rootcause')->nullable();
            $table->string('recovery')->nullable();
            $table->string('assy_trial_check')->nullable();
            $table->enum('judge_permanen', ['OK', 'NG'])->nullable();

            // ── Durasi & Meta ──
            $table->unsignedInteger('durasi_on_process_seconds')->nullable();
            $table->unsignedInteger('durasi_on_trial_seconds')->nullable();
            $table->unsignedInteger('durasi_total_seconds')->nullable();
            $table->unsignedInteger('repair_count')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_repair_histories');
        Schema::dropIfExists('request_repairs');
    }
};