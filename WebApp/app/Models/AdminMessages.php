<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employees as Employee; 
use App\Models\Attendance_Master;

class AdminMessages extends Model
{
    use HasFactory;

    protected $primaryKey = 'message_id';
    protected $fillable = [
        'attendance_id',
        'message_from',
        'message_to',
        'message',
        'message_status',
        'parent_id',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance_Master::class, 'attendance_id', 'attendance_id');
    }

    public function sender()
    {
        return $this->belongsTo(Employee::class, 'message_from', 'employee_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Employee::class, 'message_to', 'employee_id');
    }
}
