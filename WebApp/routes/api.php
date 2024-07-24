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
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\EmployeeMessageController;
use App\Http\Controllers\DisplayHolidays;

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
Route::get('display_holidays', [DisplayHolidays::class, 'holidays']);

// post api's
Route::post('leave_application', [LeaveApplicationController::class, 'store']);

Route::prefix('leave')->group(function () {
    Route::post('/approve/{id}', [LeaveApprovalController::class, 'approve'])->name('leave_approve');
});

Route::prefix('leave')->group(function () {
    Route::post('/reject/{id}', [LeaveRejectController::class, 'reject'])->name('leave_reject');
});


// Messages API's

// Admin Messages
Route::get('/display_admin_messages', [AdminMessageController::class, 'displayMessages']);
Route::post('/send_message', [AdminMessageController::class, 'sendMessage']);
Route::post('/send_reply', [AdminMessageController::class, 'sendReply']);
Route::post('/update_message_status', [AdminMessageController::class, 'updateMessageStatus']);
Route::post('/clear_messages', [AdminMessageController::class, 'clearMessages']);
Route::get('/display_thread_messages/{parentId}', [AdminMessageController::class, 'displayThreadMessages']);

// Employee Messages
Route::get('/display_emp_messages', [EmployeeMessageController::class, 'displayMessages']);
Route::post('/emp_send_reply', [EmployeeMessageController::class, 'sendReply']);

Route::post('/emp_update_message_status', [EmployeeMessageController::class, 'updateMessageStatus']);
Route::post('/emp_clear_messages', [EmployeeMessageController::class, 'clearMessages']);
Route::get('/emp_display_thread_messages/{parentId}', [EmployeeMessageController::class, 'displayThreadMessages']);