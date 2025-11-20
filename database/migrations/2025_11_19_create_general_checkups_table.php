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
        Schema::create('general_checkups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->string('kode_barang');
            $table->string('gambar')->nullable();
            $table->string('nama');
            $table->string('line')->nullable();
            $table->date('tanggal_terjadwal');
            $table->date('tanggal_checkup')->nullable();
            $table->timestamp('mulai_perbaikan')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->enum('status', ['pending', 'on_process', 'finish'])->default('pending');
            $table->text('catatan_umum')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('schedule_id');
            $table->index('barang_id');
            $table->index('status');
            $table->index('tanggal_terjadwal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_checkups');
    }
};