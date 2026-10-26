<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Status baru "finish_repair" di antara on_process dan on_trial:
 *   open -> on_process -> finish_repair -> on_trial -> closed
 *
 * + kolom finish_repair_at (waktu repair selesai / durasi repair berhenti)
 *   di request_repairs, request_repair_attempts, request_repair_histories.
 *
 * Aman dijalankan ulang. Daftar ENUM dibaca dari database (bukan di-hardcode)
 * karena nilai 'open' dulu ditambahkan di luar migration.
 */
return new class extends Migration
{
    private array $tables = ['request_repairs', 'request_repair_attempts', 'request_repair_histories'];

    public function up(): void
    {
        $values = $this->statusEnumValues();
        if ($values && !in_array('finish_repair', $values, true)) {
            $pos = array_search('on_process', $values, true);
            array_splice($values, $pos === false ? count($values) : $pos + 1, 0, 'finish_repair');
            $this->setStatusEnum($values);
        }

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'finish_repair_at')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $column = $table->timestamp('finish_repair_at')->nullable();
                    if (Schema::hasColumn($tableName, 'on_process_at')) {
                        $column->after('on_process_at');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // Data yang sedang finish_repair dikembalikan ke on_process sebelum nilai ENUM dihapus
        DB::table('request_repairs')->where('status', 'finish_repair')->update(['status' => 'on_process']);

        $values = $this->statusEnumValues();
        if ($values && in_array('finish_repair', $values, true)) {
            $this->setStatusEnum(array_values(array_diff($values, ['finish_repair'])));
        }

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'finish_repair_at')) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropColumn('finish_repair_at'));
            }
        }
    }

    /** Nilai ENUM kolom status saat ini, mis. ['open','on_process','on_trial','closed'] */
    private function statusEnumValues(): array
    {
        $column = DB::selectOne("SHOW COLUMNS FROM request_repairs WHERE Field = 'status'");
        if (!$column || !preg_match("/^enum\((.*)\)$/i", $column->Type, $m)) {
            return [];
        }

        return array_map(fn ($v) => trim($v, "'"), str_getcsv($m[1], ',', "'"));
    }

    private function setStatusEnum(array $values): void
    {
        $column  = DB::selectOne("SHOW COLUMNS FROM request_repairs WHERE Field = 'status'");
        $list    = implode(',', array_map(fn ($v) => DB::getPdo()->quote($v), $values));
        $default = $column->Default !== null ? ' DEFAULT ' . DB::getPdo()->quote($column->Default) : '';
        $null    = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';

        DB::statement("ALTER TABLE request_repairs MODIFY status ENUM({$list}) {$null}{$default}");
    }
};
