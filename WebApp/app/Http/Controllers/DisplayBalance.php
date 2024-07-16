<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave_Balance as Balance;
use App\Models\Employees as Employee;
use App\Models\Attendance_Master;
use App\Models\Leave_Master;

class DisplayBalance extends Controller
{
    public function balance()
    {
        // Fetch all leave balances
        $balances = Balance::all();

        // Prepare the response array
        $response = [
            'status' => 200,
            'data' => []
        ];

        // Iterate through each balance to fetch employee name, leave type, and attendance dates
        foreach ($balances as $balance) {
            // Fetch employee name from Employee model
            $employee = Employee::find($balance->employee_id);
            if ($employee) {
                $employee_name = $employee->name;
            } else {
                $employee_name = 'Unknown'; // Handle if employee is not found
            }

            // Fetch leave type from Leave_Master model
            $leave = Leave_Master::where('leave_id', $balance->leave_id)->first();
            if ($leave) {
                $leave_type = $leave->leave_type;
            } else {
                $leave_type = 'Unknown'; // Handle if leave type is not found
            }

            // Fetch all attendance dates from Attendance_Master model
            $attendances = Attendance_Master::where('employee_id', $balance->employee_id)
                ->orderBy('date', 'asc')
                ->get();

            // Prepare attendance data in the required format
            $attendance_data = [];
            foreach ($attendances as $attendance) {
                $attendance_data[] = [
                    'attendance_id' => $attendance->id,
                    'employee_id' => $attendance->employee_id,
                    'punch_in' => $attendance->punch_in,
                    'punch_out' => $attendance->punch_out,
                    'date' => $attendance->date,
                    'created_at' => $attendance->created_at,
                    'updated_at' => $attendance->updated_at,
                ];
            }

            // Add employee name, leave type, balance, and attendance dates to the response data
            $response['data'][] = [
                'employee_name' => $employee_name,
                'leave_type' => $leave_type,
                'balance' => $balance->balance,
                'attendance_dates' => $attendance_data,
            ];
        }

        return response()->json($response);
    }
}
