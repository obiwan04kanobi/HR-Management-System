<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compoff_Master;
use App\Models\Holidays_Master;
use App\Models\Employees as Employee;

class DisplayCompoffs extends Controller
{
    public function display_compoffs()
    {
        // Using join to get holiday names and employee names along with compoff data
        $display = Compoff_Master::join('holidays_masters', 'compoff_master.holiday_id', '=', 'holidays_masters.holiday_id')
            ->join('employees', 'compoff_master.employee_id', '=', 'employees.employee_id')
            ->select('compoff_master.*', 'holidays_masters.holiday_name', 'employees.name as employee_name') // Select relevant columns
            ->get();

        $data = [
            'status' => 200,
            'Compoffs' => $display
        ];

        return response()->json($data, 200);
    }

    // Function to update compoff details
    public function update_compoff(Request $request)
    {
        $compoff = Compoff_Master::find($request->compoff_id);

        if ($compoff) {
            $compoff->compoff_taken_date = $request->compoff_taken_date;
            $compoff->remarks = $request->remarks;
            $compoff->status = 1; // 1 for pending status but submitted
            $compoff->save();

            return response()->json([
                'status' => 200,
                'message' => 'Compensatory leave updated successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Compensatory leave not found.',
            ]);
        }
    }
}

