<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\DisplayEmployees;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DisplayBalance;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\LeaveMasterController;
use App\Http\Controllers\LeaveApplicationDisplay;
use App\Http\Controllers\LeaveApprovalController;
use App\Http\Controllers\LeaveRejectController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API's 

// get api's
Route::get('display_employees',[DisplayEmployees::class, 'employees']);
Route::get('filter_employees', [AttendanceController::class, 'filterEmployees']);
Route::get('display_balance', [DisplayBalance::class, 'balance']);
Route::get('leaves', [LeaveMasterController::class, 'leave_types']);
Route::get('display_leaves', [LeaveApplicationDisplay::class, 'Leaves']);


// post api's
Route::post('leave_application', [LeaveApplicationController::class, 'store']);

Route::prefix('leave')->group(function () {
    Route::post('/approve/{id}', [LeaveApprovalController::class, 'approve'])->name('leave_approve');
});

Route::prefix('leave')->group(function () {
    Route::post('/reject/{id}', [LeaveRejectController::class, 'reject'])->name('leave_reject');
});