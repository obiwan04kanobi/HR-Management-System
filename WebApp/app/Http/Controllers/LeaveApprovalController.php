<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplicationMaster;

class LeaveApprovalController extends Controller
{
    public function approve(Request $request, $id)
    {
        try {
            $leaveApplication = LeaveApplicationMaster::findOrFail($id);
            $leaveApplication->status = 0; // Update status according to your application's logic
            $leaveApplication->save();
    
            return response()->json(['message' => 'Leave application approved successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to approve leave application', 'message' => $e->getMessage()], 500);
        }
    }

    public function approveMultiple(Request $request)
    {
        try {
            $application_ids = $request->input('application_ids');

            foreach ($application_ids as $id) {
                $leaveApplication = LeaveApplicationMaster::findOrFail($id);
                $leaveApplication->status = 0; // Update status according to your application's logic
                $leaveApplication->save();
            }

            return response()->json(['message' => 'Leave applications approved successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to approve leave applications', 'message' => $e->getMessage()], 500);
        }
    }
}

