<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance_Master as Attendance;
use App\Models\Employees as Employee; // Assuming the model for employees is Employee_Master
use App\Models\Leave_Master as LeaveType; // Assuming the model for leave types is Leave_Master

class AttendanceController extends Controller
{
    public function filterEmployees(Request $request)
    {
        // Retrieve input parameters
        $employeeId = $request->input('employee_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Initialize query
        $query = Attendance::query();

        // Apply filters if provided
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($fromDate && $toDate) {
            // Convert dates to Carbon instances for proper formatting
            $fromDateFormatted = Carbon::parse($fromDate)->format('Y-m-d');
            $toDateFormatted = Carbon::parse($toDate)->format('Y-m-d');

            $query->whereDate('date', '>=', $fromDateFormatted)
                  ->whereDate('date', '<=', $toDateFormatted);
        }

        // Order by employee_id and date
        $query->orderBy('employee_id')
              ->orderBy('date', 'asc');

        // Retrieve filtered data
        $attendances = $query->get();

        // Attach employee names, leave types, and report_to to the attendance data
        $data = $attendances->map(function ($attendance) {
            $employee = Employee::find($attendance->employee_id);
            $leaveType = $this->determineLeaveType($attendance);
            $reportTo = $employee ? $employee->report_to : null;

            return [
                'employee_id' => $attendance->employee_id,
                'employee_name' => $employee ? $employee->name : 'Unknown',
                'date' => $attendance->date,
                'leave_type' => $leaveType,
                'report_to' => $reportTo,
                'attendance_id' => $attendance->attendance_id,
                // 'message' => $attendance->message,
                // 'message_status' => $attendance->message_status,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    // Method to determine leave type based on attendance
    private function determineLeaveType($attendance)
    {
        // Assuming you have a method to determine leave type based on punch_in time
        if (!$attendance->punch_in) {
            return 'Absent';
        }

        $punchInTime = Carbon::parse($attendance->punch_in);
        $punchOutTime = Carbon::parse($attendance->punch_out);
        
        if ($punchInTime->greaterThan(Carbon::parse('14:00:00'))){
            return 'Absent';
        }
        elseif ($punchInTime->lessThanOrEqualTo(Carbon::parse('09:30:00')) && $punchOutTime->greaterThanOrEqualTo(Carbon::parse('18:30:00'))) {
            return 'Present';
        }
        elseif ($punchInTime->greaterThan(Carbon::parse('09:30:00')) && $punchInTime->lessThanOrEqualTo(Carbon::parse('11:30:00')) && $punchOutTime->greaterThanOrEqualTo(Carbon::parse('18:30:00'))) {
            return 'Short Leave--Present';
        }
        elseif ($punchInTime->lessThanOrEqualTo(Carbon::parse('09:31:00')) && $punchOutTime->greaterThanOrEqualTo(Carbon::parse('16:30:00')) && $punchOutTime->lessThan(Carbon::parse('18:30:00'))) {
            return 'Present--Short Leave';
        }
        elseif ($punchInTime->diffInHours($punchOutTime) >= 7) {
            return 'Short Leave';
        }
        elseif ($punchInTime->diffInHours($punchOutTime) >= 9) {
            return 'Present';
        }   
        elseif ($punchInTime->lessThanOrEqualTo(Carbon::parse('09:31:00')) && $punchOutTime->greaterThanOrEqualTo(Carbon::parse('14:00:00')) && $punchOutTime->lessThanOrEqualTo(Carbon::parse('18:30:00'))) {
            return '2nd Half Absent';
        } 
        elseif ($punchInTime->greaterThan(Carbon::parse('09:30:00')) && $punchInTime->lessThanOrEqualTo(Carbon::parse('14:00:00')) && $punchOutTime->greaterThanOrEqualTo(Carbon::parse('18:30:00'))) {
            return '1st Half Absent';
        } 
        else {
            return 'Absent';
        }
    }
}