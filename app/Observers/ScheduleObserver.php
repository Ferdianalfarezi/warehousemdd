<?php

namespace App\Observers;

use App\Models\Schedule;
use App\Models\GeneralCheckup;
use Illuminate\Support\Facades\Log;

class ScheduleObserver
{
    /**
     * Handle the Schedule "updated" event.
     * Auto-populate general checkups when schedule status becomes "hari_ini" only
     */
    public function updated(Schedule $schedule)
    {
        try {
            // Check if status is "hari_ini" only (exclude "segera")
            if ($schedule->status === 'hari_ini') {
                // Check if already exists
                $exists = GeneralCheckup::where('schedule_id', $schedule->id)
                    ->whereIn('status', ['pending', 'on_process'])
                    ->exists();

                if (!$exists) {
                    // Create general checkup
                    GeneralCheckup::create([
                        'schedule_id' => $schedule->id,
                        'barang_id' => $schedule->barang_id,
                        'kode_barang' => $schedule->kode_barang,
                        'gambar' => $schedule->gambar,
                        'nama' => $schedule->nama,
                        'line' => $schedule->barang->line ?? null,
                        'tanggal_terjadwal' => $schedule->service_berikutnya,
                        'status' => 'pending',
                    ]);

                    Log::info("Auto-populated checkup for schedule ID: {$schedule->id}");
                }
            }
        } catch (\Exception $e) {
            Log::error("ScheduleObserver Error: " . $e->getMessage());
        }
    }

    /**
     * Handle the Schedule "saved" event.
     * This will trigger on both create and update
     */
    public function saved(Schedule $schedule)
    {
        // Call updated logic
        $this->updated($schedule);
    }
}