<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employees extends Authenticatable
{
    use HasFactory;

    // If your table name is different from the plural of your model name
    protected $table = 'employees';

    // Define the primary key, if different from 'id'
    protected $primaryKey = 'employee_id';

    // Specify the attributes that are mass assignable
    protected $fillable = ['name', 'email', 'password', 'department', 'designation', 'report_to', 'date_join', 'status'];

    // Disable timestamps if not using created_at and updated_at columns
    public $timestamps = false;
}
