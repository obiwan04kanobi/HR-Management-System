<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attendance_Master extends Model
{
    use HasFactory;

    protected $table = 'attendance_masters';

    protected $fillable = [
        'employee_id',
        'punch_in',
        'punch_out',
        'date'
    ];
}
