<?php

namespace App\Http\Controllers;

use App\Models\GeneralCheckup;
use Illuminate\Http\Request;

class AndonGeneralCheckupController extends Controller
{
    /**
     * Display general checkup monitoring (READ-ONLY)
     * Shows ALL checkups regardless of status
     */
    public function index()
    {
        // Get all checkups ordered by scheduled date (newest first)
        $checkups = GeneralCheckup::with(['barang', 'schedule'])
            ->orderBy('tanggal_terjadwal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('andon.general-checkup', compact('checkups'));
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