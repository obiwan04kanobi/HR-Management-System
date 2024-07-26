<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holidays_Master extends Model
{
    use HasFactory;

    protected $table = 'holidays_masters';
    protected $primaryKey = 'holiday_id';

    protected $fillable = [
        'holiday_id',
        'holiday_name',
        'holiday_date',
        'attendance_id',
    ];
}
