<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance_Master as Attendance;
use App\Models\Employees as Employee;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'messages';

    // Define the primary key column
    protected $primaryKey = 'message_id';

    public $incrementing = false; // If your primary key is not auto-incrementing

    protected $fillable = [
        'message_id',
        'attendance_id',
        'message_from',
        'message_to',
        'message',
        'message_status'
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id', 'attendance_id');
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
