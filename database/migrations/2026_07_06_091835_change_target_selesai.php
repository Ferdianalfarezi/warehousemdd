<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            $table->dropColumn('target_selesai');
            $table->integer('kekuatan_stock_fg')->nullable()->after('kategori_problem');
        });
    }

    public function down(): void
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            $table->dropColumn('kekuatan_stock_fg');
            $table->date('target_selesai')->nullable();
        });
    }
};