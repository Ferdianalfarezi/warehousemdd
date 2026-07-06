<?php

namespace App\Http\Controllers;

use App\Models\RequestRepair;

class AndonRequestRepairController extends Controller
{
    /**
     * Display request repair monitoring (READ-ONLY)
     * Shows ALL active request repairs (open, on_process, on_trial)
     * Closed items are excluded because they're already moved to history table
     */
    public function index()
    {
        $requestRepairs = RequestRepair::with(['barang', 'creator'])
            ->orderByRaw("FIELD(status, 'on_process', 'on_trial', 'open')")
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