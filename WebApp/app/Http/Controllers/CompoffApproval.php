<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compoff_Master;


class CompoffApproval extends Controller
{
    public function approve(Request $request, $id)
    {
        try {
            $CompoffApplication = Compoff_Master::findOrFail($id);
            $CompoffApplication->status = 0; // Update status according to your application's logic
            $CompoffApplication->save();
    
            return response()->json(['message' => 'Compoff application approved successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to approve Compoff application', 'message' => $e->getMessage()], 500);
        }
    }

    public function approveMultiple(Request $request)
    {
        try {
            $application_ids = $request->input('compoff_id');

            foreach ($application_ids as $id) {
                $CompoffApplication = Compoff_Master::findOrFail($id);
                $CompoffApplication->status = 0; // Update status according to your application's logic
                $CompoffApplication->save();
            }

            return response()->json(['message' => 'Compoff application approved successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to approve Compoff application', 'message' => $e->getMessage()], 500);
        }
    }

    public function reject($id)
    {
        // Logic to update the status of leave application with $id
        try {
            $CompoffApplication = Compoff_Master::findOrFail($id);
            $CompoffApplication->status = 2; // Assuming 2 means rejected, adjust as per your status logic
            $CompoffApplication->save();

            return response()->json(['message' => 'Compoff application rejected successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to reject Compoff application', 'message' => $e->getMessage()], 500);
        }
    }

    public function rejectMultiple(Request $request)
    {
        try {
            $application_ids = $request->input('compoff_id');

            foreach ($application_ids as $id) {
                $CompoffApplication = Compoff_Master::findOrFail($id);
                $CompoffApplication->status = 2; // Assuming 2 means rejected, adjust as per your status logic
                $CompoffApplication->save();
            }

            return response()->json(['message' => 'Compoff applications rejected successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to reject Compoff applications', 'message' => $e->getMessage()], 500);
        }
    }
}
