<?php

namespace App\Http\Controllers;

use App\Models\GeneralCheckup;
use App\Models\Schedule;
use Illuminate\Http\Request;

class AndonGeneralCheckupController extends Controller
{
    /**
     * Display general checkup monitoring (READ-ONLY)
     * Menampilkan checkup yang sedang on process + 4 schedule upcoming terdekat
     */
    public function index()
    {
        // Semua yang sedang on process,
        // urut dari durasi pengerjaan paling lama (mulai perbaikan paling awal)
        $checkups = GeneralCheckup::with('starter:id,nama')
            ->where('status', 'on_process')
            ->orderByRaw('mulai_perbaikan IS NULL') // yang belum mulai taruh paling bawah
            ->orderBy('mulai_perbaikan', 'asc')
            ->get();

        // Schedule yang sedang dikerjakan (biar tidak dobel tampil)
        $onProcessScheduleIds = $checkups->pluck('schedule_id')->filter()->unique();

        // 4 schedule terdekat (hari ini ke depan)
        $upcomings = Schedule::whereDate('service_berikutnya', '>=', today())
            ->when($onProcessScheduleIds->isNotEmpty(), function ($query) use ($onProcessScheduleIds) {
                $query->whereNotIn('id', $onProcessScheduleIds);
            })
            ->orderBy('service_berikutnya', 'asc')
            ->limit(4)
            ->get();

        return view('andon.general-checkup', compact('checkups', 'upcomings'));
    }

    /**
     * Display single checkup detail (optional, for detail modal if needed)
     */
    public function show($id)
    {
        $checkup = GeneralCheckup::with(['barang', 'schedule', 'details', 'partReplacements'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $checkup
        ]);
    }
}