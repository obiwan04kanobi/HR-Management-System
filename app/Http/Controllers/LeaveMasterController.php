<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave_Master as Leave;

class LeaveMasterController extends Controller
{
    public function leave_types()
    {
        // Fetch leave types with days greater than zero
        $display = Leave::where('days', '>', 0)
                        ->select('leave_id', 'leave_type', 'days', 'status')
                        ->get();

        $data = [
            'status' => 200,
            'Leaves' => $display
        ];

        return response()->json($data, 200);
    }
}
