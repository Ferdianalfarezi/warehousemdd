<?php
// database/migrations/xxxx_add_ng_action_columns_to_checkup_details_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('checkup_details', function (Blueprint $table) {
            $table->enum('ng_action_type', ['part', 'inhouse', 'outhouse'])->nullable()->after('catatan');
            $table->string('ng_action_status')->nullable()->after('ng_action_type');
        });
    }

    public function down()
    {
        Schema::table('checkup_details', function (Blueprint $table) {
            $table->dropColumn(['ng_action_type', 'ng_action_status']);
        });
    }
};