<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Leave_Balance as LeaveBalance;
use App\Models\Employees as Employee;

class LeaveBalanceSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::all();
        foreach ($employees as $employee) {
            LeaveBalance::updateOrCreate(
                ['employee_id' => $employee->employee_id],
                ['balance' => 30, 'leave_id' => 1] // Assuming leave_id 1 is the default leave type
            );
        }
    }
}
