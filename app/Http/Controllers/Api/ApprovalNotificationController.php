<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PddRequest;
use App\Models\SubcontRequest;
use Illuminate\Http\Request;

class ApprovalNotificationController extends Controller
{
    public function getPendingCount(Request $request)
    {
        $inhouseCount = PddRequest::where('status', 'pending')->count();
        $outhouseCount = SubcontRequest::where('status', 'pending')->count();
        
        return response()->json([
            'success' => true,
            'inhouse' => $inhouseCount,
            'outhouse' => $outhouseCount,
            'total' => $inhouseCount + $outhouseCount
        ]);
    }
}