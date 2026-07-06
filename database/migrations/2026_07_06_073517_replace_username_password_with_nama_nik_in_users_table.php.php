<?php
// database/migrations/2026_07_06_000000_replace_username_password_with_nama_nik_in_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom dulu TANPA unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama')->nullable()->after('id');
            $table->string('nik', 10)->nullable()->after('nama');
        });

        // 2. Backfill: nama dari username, nik dari placeholder unik sementara
        //    (misal 'TMP' + id, biar ga bentrok) — WAJIB diganti manual jadi NIK asli nanti
        DB::table('users')->orderBy('id')->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'nama' => $user->username ?? ('User ' . $user->id),
                'nik'  => str_pad((string) $user->id, 10, '0', STR_PAD_LEFT),
            ]);
        });

        // 3. Baru set NOT NULL + unique setelah semua baris punya nilai unik
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama')->nullable(false)->change();
            $table->string('nik', 10)->nullable(false)->change();
            $table->unique('nik');
        });

        // 4. Drop kolom lama
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'password']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('nama');
            $table->string('password')->after('nik');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'nik']);
        });
    }
};