<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplicationMaster as Leaves;
use App\Models\Employees;
use App\Models\Leave_Master;

class LeaveApplicationDisplay extends Controller
{
    public function Leaves()
    {
        // Fetch leaves with employee names and leave types using joins
        $leaves = Leaves::join('employees', 'leave_application_masters.employee_id', '=', 'employees.employee_id')
                        ->join('leave_masters', 'leave_application_masters.leave_type', '=', 'leave_masters.leave_id')
                        ->select('leave_application_masters.*', 'employees.name as employee_name', 'leave_masters.leave_type', 'employees.report_to as report_to')
                        ->where('leave_application_masters.status', 1) // Filter by status = 1
                        ->get();

        $data = [
            'status' => 200,
            'Leaves' => $leaves
        ];
        
        return response()->json($data, 200);
    } 
}
