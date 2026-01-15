<?php
// app/Http/Controllers/HistoryRequestPartController.php

namespace App\Http\Controllers;

use App\Models\HistoryRequestPart;
use Illuminate\Http\Request;

class HistoryRequestPartController extends Controller
{
    public function index()
    {
        $histories = HistoryRequestPart::with(['items', 'user'])
            ->latest()
            ->get();
        
        return view('history-request-parts.index', compact('histories'));
    }

    public function show(HistoryRequestPart $historyRequestPart)
    {
        $historyRequestPart->load(['items.part', 'user', 'verifiedBy']);
        
        return response()->json([
            'success' => true,
            'data' => $historyRequestPart
        ]);
    }
}