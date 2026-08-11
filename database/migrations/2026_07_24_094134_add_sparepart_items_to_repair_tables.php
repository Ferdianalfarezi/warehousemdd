<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('request_repairs', function (Blueprint $table) {
            $table->json('sparepart_items')->nullable()->after('catatan_penggantian_sparepart');
        });
        Schema::table('request_repair_attempts', function (Blueprint $table) {
            $table->json('sparepart_items')->nullable()->after('catatan_penggantian_sparepart');
        });
        Schema::table('request_repair_histories', function (Blueprint $table) {
            $table->json('sparepart_items')->nullable()->after('catatan_penggantian_sparepart');
        });
    }

    public function down()
    {
        Schema::table('request_repairs', fn (Blueprint $t) => $t->dropColumn('sparepart_items'));
        Schema::table('request_repair_attempts', fn (Blueprint $t) => $t->dropColumn('sparepart_items'));
        Schema::table('request_repair_histories', fn (Blueprint $t) => $t->dropColumn('sparepart_items'));
    }
};