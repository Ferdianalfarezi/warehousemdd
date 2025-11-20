<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->unique()->constrained('barangs')->onDelete('cascade');
            $table->string('gambar')->nullable();
            $table->string('kode_barang');
            $table->string('nama');
            $table->date('mulai_service');
            $table->enum('periode', ['harian', 'mingguan', 'bulanan', 'custom']);
            $table->integer('interval_value');
            $table->date('service_berikutnya');
            $table->date('terakhir_service')->nullable();
            $table->enum('status', ['terjadwal', 'segera', 'hari_ini', 'terlambat'])->default('terjadwal');
            $table->integer('hari_terlambat')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};