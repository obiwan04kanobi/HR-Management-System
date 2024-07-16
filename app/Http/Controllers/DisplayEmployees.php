<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;

class DisplayEmployees extends Controller
{
    public function employees()
    {
        $display = Employees::all();

        $data = [
            'status' => 200,
            'Employees_Name' => $display
        ];
        
        return response()->json($data,200);
    } 
}
