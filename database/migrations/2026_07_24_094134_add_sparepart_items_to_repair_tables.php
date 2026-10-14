<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['request_repairs', 'request_repair_attempts', 'request_repair_histories'];

    public function up()
    {
        // Aman dijalankan ulang: di sebagian server kolom ini sudah pernah ditambahkan manual
        foreach ($this->tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'sparepart_items')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->json('sparepart_items')->nullable()->after('catatan_penggantian_sparepart');
                });
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasColumn($tableName, 'sparepart_items')) {
                Schema::table($tableName, fn (Blueprint $t) => $t->dropColumn('sparepart_items'));
            }
        }
    }
};
