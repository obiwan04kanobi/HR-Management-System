<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplicationMaster;

class LeaveRejectController extends Controller
{
    public function reject($id)
    {
        // Logic to update the status of leave application with $id
        try {
            $leaveApplication = LeaveApplicationMaster::findOrFail($id);
            $leaveApplication->status = 2; // Assuming 2 means rejected, adjust as per your status logic
            $leaveApplication->save();

            return response()->json(['message' => 'Leave application rejected successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to reject leave application', 'message' => $e->getMessage()], 500);
        }
    }

    public function rejectMultiple(Request $request)
    {
        try {
            $application_ids = $request->input('application_ids');

            foreach ($application_ids as $id) {
                $leaveApplication = LeaveApplicationMaster::findOrFail($id);
                $leaveApplication->status = 2; // Assuming 2 means rejected, adjust as per your status logic
                $leaveApplication->save();
            }

            return response()->json(['message' => 'Leave applications rejected successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to reject leave applications', 'message' => $e->getMessage()], 500);
        }
    }
}
