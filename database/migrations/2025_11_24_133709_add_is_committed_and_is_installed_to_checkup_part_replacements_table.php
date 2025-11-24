<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('checkup_part_replacements', function (Blueprint $table) {
            $table->boolean('is_committed')->default(false)->after('catatan');
            $table->boolean('is_installed')->default(false)->after('is_committed');
        });
    }

    public function down()
    {
        Schema::table('checkup_part_replacements', function (Blueprint $table) {
            $table->dropColumn(['is_committed', 'is_installed']);
        });
    }
};