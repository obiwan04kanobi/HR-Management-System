<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employees as Employee;
use App\Models\Attendance_Master;
use App\Models\Holidays_Master as Holidays;

class Compoff_Master extends Model
{
    use HasFactory;

    protected $table = 'compoff_master';
    protected $primaryKey = 'compoff_id';
    protected $fillable = ['holiday_id','employee_id', 'date', 'compoff_taken_date', 'remarks','status'];

    public function holiday_master()
    {
        return $this->belongsTo(Holidays::class, 'holiday_id', 'holiday_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance_Master::class, 'date', 'date');
    }

    public function getEmployeeNameAttribute()
    {
        return $this->employee->employee_name;
    }
}
