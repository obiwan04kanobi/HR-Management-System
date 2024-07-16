<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Employees;

class EmployeeLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        \Log::info('Login attempt: ', $credentials);
    
        // Fetch the employee by email
        $employee = Employees::where('email', $credentials['email'])->first();
    
        if ($employee) {
            \Log::info('Employee found: ', ['email' => $employee->email, 'password' => $employee->password]);
    
            // Check if the password is not null and matches the entered password
            if ($employee->password && Hash::check($credentials['password'], $employee->password)) {
                \Log::info('Password match for user: ' . $credentials['email']);
    
                // Attempt to log in the user
                Auth::guard('web')->login($employee);
    
                if (Auth::check()) {
                    \Log::info('Login successful for user: ' . $credentials['email']);
                    return redirect()->intended('/')->with('login_success', true);
                } else {
                    \Log::warning('Auth attempt failed for user: ' . $credentials['email']);
                }
            } else {
                \Log::warning('Password mismatch for user: ' . $credentials['email']);
            }
        } else {
            \Log::warning('No employee found with email: ' . $credentials['email']);
        }
    
        return back()->withErrors(['email' => 'Incorrect email or password'])->withInput();
    }    
}
