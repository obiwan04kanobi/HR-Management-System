<?php

// app/Models/LeaveApplicationMaster.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplicationMaster extends Model
{
    use HasFactory;

    protected $table = 'leave_application_masters';

    protected $primaryKey = 'application_id'; // Specify your primary key column name

    protected $fillable = [
        'employee_id',
        'from_date',
        'to_date',
        'leave_type',
        'session',
        'half',
        'remarks',
        'status',
    ];
}
