<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave_Master extends Model
{
    use HasFactory;

    protected $table = 'leave_masters';

    protected $fillable = [
        'leave_id',
        'leave_type',
        'days',
        'status'
    ];
}
