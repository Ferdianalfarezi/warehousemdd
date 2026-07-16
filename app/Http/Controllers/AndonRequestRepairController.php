<?php

namespace App\Http\Controllers;

use App\Models\RequestRepair;

class AndonRequestRepairController extends Controller
{
    /**
     * Display request repair monitoring (READ-ONLY)
     * Shows ALL active request repairs (open, on_process, on_trial)
     * Closed items are excluded because they're already moved to history table
     *
     * Urutan:
     * 1. Status: open -> on_process -> on_trial
     * 2. Khusus status open: diurutkan dari sisa stock FG paling kritis
     *    (paling dekat habis / udah lewat paling lama dari tanggal_pengajuan)
     * 3. Sisanya (on_process & on_trial): tanggal_pengajuan & created_at terbaru dulu
     */
    public function index()
    {
        $requestRepairs = RequestRepair::with(['barang', 'creator', 'pics'])
            ->orderByRaw("FIELD(status, 'open', 'on_process', 'on_trial')")
            ->orderByRaw("
                CASE
                    WHEN status = 'open' THEN (kekuatan_stock_fg - DATEDIFF(NOW(), tanggal_pengajuan))
                    ELSE NULL
                END ASC
            ")
            ->orderBy('tanggal_pengajuan', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('andon.request-repair', compact('requestRepairs'));
    }

    /**
     * Display single request repair detail (for detail modal if needed)
     */
    public function show($id)
    {
        $requestRepair = RequestRepair::with(['barang', 'creator'])->findOrFail($id);

        $data                    = $requestRepair->toArray();
        $data['gambar_url']      = $requestRepair->gambar_url;
        $data['created_by_name'] = $requestRepair->creator->nama ?? '-';

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}