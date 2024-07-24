<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holidays_Master as Holidays;

class DisplayHolidays extends Controller
{
    public function holidays()
    {
        $display = Holidays::all();

        $data = [
            'status' => 200,
            'Holidays' => $display
        ];
        
        return response()->json($data,200);
    }
}
