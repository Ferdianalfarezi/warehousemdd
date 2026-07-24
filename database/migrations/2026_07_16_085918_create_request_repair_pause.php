<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_repair_pauses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_repair_id')->nullable()->index();
            $table->string('no', 50);
            $table->integer('cycle_number')->default(1);
            $table->string('alasan', 30);
            $table->dateTime('paused_at');
            $table->dateTime('resumed_at')->nullable();
            $table->unsignedBigInteger('paused_by')->nullable();
            $table->integer('durasi_paused_seconds')->nullable();
            $table->timestamps();

            $table->index(['no', 'cycle_number']);
        });

        Schema::table('request_repairs', function (Blueprint $table) {
            $table->boolean('is_paused')->default(false)->after('status');
            $table->dateTime('paused_at')->nullable()->after('is_paused');
            $table->string('pause_reason', 30)->nullable()->after('paused_at');
            $table->integer('total_paused_seconds')->default(0)->after('pause_reason');
            $table->integer('cycle_number')->default(1)->after('total_paused_seconds');
        });

        Schema::table('request_repair_histories', function (Blueprint $table) {
            $table->integer('total_paused_seconds')->default(0)->after('ng_attempt_count');
            $table->integer('cycle_number')->default(1)->after('total_paused_seconds');
        });

        Schema::table('request_repair_attempts', function (Blueprint $table) {
            $table->integer('total_paused_seconds')->default(0)->after('durasi_on_trial_seconds');
            $table->integer('cycle_number')->default(1)->after('total_paused_seconds');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_repair_pauses');
        Schema::table('request_repairs', fn (Blueprint $t) => $t->dropColumn(['is_paused', 'paused_at', 'pause_reason', 'total_paused_seconds', 'cycle_number']));
        Schema::table('request_repair_histories', fn (Blueprint $t) => $t->dropColumn(['total_paused_seconds', 'cycle_number']));
        Schema::table('request_repair_attempts', fn (Blueprint $t) => $t->dropColumn(['total_paused_seconds', 'cycle_number']));
    }
};