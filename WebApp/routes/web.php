<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Auth\EmployeeRegisterController;
use App\Http\Controllers\Auth\EmployeeLogoutController;

use App\Http\Controllers\Auth\ResetPasswordController;



// Authentication Routes

// Login Routes
Route::get('/login', [EmployeeLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [EmployeeLoginController::class, 'login']);

// Registration Routes
Route::get('/register', [EmployeeRegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [EmployeeRegisterController::class, 'register']);

// Logout Route
Route::post('/logout', [EmployeeLogoutController::class, 'logout'])->name('logout');


// Route to show reset password form
Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset')->middleware('auth');

// Route to handle password update
Route::post('/reset-password', [ResetPasswordController::class, 'updatePassword'])->name('password.update')->middleware('auth');




// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/emp_attendance', function () {
        return view('attendance');
    });

    Route::get('/adm_attendance', function () {
        return view('adm_attendance');
    });

    Route::get('/leave', function () {
        return view('leave_form');
    });

    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('/leave_approve', function () {
        return view('leave_approve');
    });

    Route::get('/compoff', function () {
        return view('compoff_from');
    });

    Route::get('/compoff_approve', function () {
        return view('compoff_approve');
    });
    
});
