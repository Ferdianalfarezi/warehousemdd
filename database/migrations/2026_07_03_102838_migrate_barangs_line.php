<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom line_id (nullable dulu, biar aman buat data existing)
        Schema::table('barangs', function (Blueprint $table) {
            $table->foreignId('line_id')->nullable()->after('model')->constrained('lines')->nullOnDelete();
        });

        // 2. Migrasi data lama: setiap nilai unik di kolom 'line' varchar -> jadi row baru di 'lines'
        //    (mesin dibiarkan null, silakan diisi manual belakangan lewat master Line)
        $existingLines = DB::table('barangs')
            ->whereNotNull('line')
            ->where('line', '!=', '')
            ->distinct()
            ->pluck('line');

        foreach ($existingLines as $namaLine) {
            $lineId = DB::table('lines')->insertGetId([
                'nama_line'  => $namaLine,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('barangs')->where('line', $namaLine)->update(['line_id' => $lineId]);
        }

        // 3. Hapus kolom lama
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('line');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('line')->nullable()->after('model');
        });

        DB::table('barangs')
            ->join('lines', 'barangs.line_id', '=', 'lines.id')
            ->update(['barangs.line' => DB::raw('lines.nama_line')]);

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('line_id');
        });
    }
};