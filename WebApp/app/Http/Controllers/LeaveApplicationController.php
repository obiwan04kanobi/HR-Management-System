<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplicationMaster; // Make sure to adjust the namespace if needed

class LeaveApplicationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_type' => 'required|exists:leave_masters,leave_id',
            'session' => 'required|string|max:50',
            'half' => 'string|max:50',
            'remarks' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);
    
        // Create a new leave application record
        $leaveApplication = LeaveApplicationMaster::create($validatedData);
    
        // Return a response indicating success
        return response()->json(['status' => 201, 'message' => 'Leave application submitted successfully'], 201);
    }
    
}
