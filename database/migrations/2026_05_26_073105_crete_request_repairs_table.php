<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_repairs', function (Blueprint $table) {
            $table->id();
            $table->string('no', 20)->unique();
            $table->date('tanggal_pengajuan');
            $table->enum('group', ['A', 'B']);
            $table->enum('shift', ['Pagi', 'Siang', 'Malam']);
            $table->integer('jumlah_stroke');
            $table->string('line_mesin', 100)->nullable();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('restrict');
            $table->string('part_no', 255);          // snapshot kode_barang saat input
            $table->string('nama', 255);             // snapshot nama barang
            $table->string('process_no', 100)->nullable();
            $table->string('customer', 255)->nullable(); // snapshot cust
            $table->enum('jenis', ['Milik Sendiri', 'Eksternal']);
            $table->date('target_selesai')->nullable();
            $table->enum('kategori_problem', ['Dies', 'Burry', 'Dimensi', 'Human Error', 'Accessories']);
            $table->text('detail_proyek')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_repairs');
    }
};