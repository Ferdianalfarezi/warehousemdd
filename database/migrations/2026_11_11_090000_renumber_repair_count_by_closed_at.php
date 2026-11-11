<?php

use App\Models\RequestRepairHistory;
use Illuminate\Database\Migrations\Migration;

/**
 * Data fix: nomor "repair ke-" (repair_count) di history repair sempat tidak urut
 * dengan waktu closed (mis. setelah waktu closed diedit). Diurutkan ulang per part_no
 * berdasarkan closed_at. Aman dijalankan berkali-kali.
 */
return new class extends Migration
{
    public function up(): void
    {
        RequestRepairHistory::query()
            ->whereNotNull('part_no')
            ->distinct()
            ->pluck('part_no')
            ->each(fn ($partNo) => RequestRepairHistory::renumberPart($partNo));
    }

    public function down(): void
    {
        // Tidak bisa dikembalikan (nomor lama memang salah urut)
    }
};
