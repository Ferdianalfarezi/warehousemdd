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
        Schema::create('history_checkups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('barang_id');
            $table->string('kode_barang');
            $table->string('gambar')->nullable();
            $table->string('nama');
            $table->string('line')->nullable();
            $table->date('tanggal_terjadwal');
            $table->date('tanggal_checkup');
            $table->timestamp('mulai_perbaikan');
            $table->timestamp('waktu_selesai');
            $table->integer('durasi_perbaikan')->comment('Durasi dalam menit');
            $table->string('status')->default('finish');
            $table->text('catatan_umum')->nullable();
            $table->integer('total_ok')->default(0);
            $table->integer('total_ng')->default(0);
            $table->integer('total_part_used')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('schedule_id');
            $table->index('barang_id');
            $table->index('tanggal_checkup');
            $table->index('line');
            $table->index('waktu_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_checkups');
    }
};