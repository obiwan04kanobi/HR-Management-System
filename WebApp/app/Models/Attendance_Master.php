<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance_Master extends Model
{
    use HasFactory;

    protected $table = 'attendance_masters';

    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'employee_id',
        'punch_in',
        'punch_out',
        'date', // Assuming this is the correct attribute for the attendance date
        'message',
        'message_status'
    ];
}
