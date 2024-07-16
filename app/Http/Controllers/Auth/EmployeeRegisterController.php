<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employees;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeeRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $employee = Employees::where('email', $request->email)->first();
        if ($employee && empty($employee->password)) {
            $employee->password = Hash::make($request->password);
            $employee->save();

            Auth::login($employee);

            return redirect('/login');
        }

        return back()->withErrors(['email' => 'This email is already registered or invalid'])->withInput();
    }
}
