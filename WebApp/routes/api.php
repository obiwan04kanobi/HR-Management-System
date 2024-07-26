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
use App\Http\Controllers\DisplayCompoffs;
use App\Http\Controllers\CompoffApproval;

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
Route::get('display_compoffs', [DisplayCompoffs::class, 'display_compoffs']);

// post api's
Route::post('leave_application', [LeaveApplicationController::class, 'store']);

Route::prefix('leave')->group(function () {
    Route::post('/approve/{id}', [LeaveApprovalController::class, 'approve'])->name('leave_approve');
});

Route::prefix('leave')->group(function () {
    Route::post('/reject/{id}', [LeaveRejectController::class, 'reject'])->name('leave_reject');
});

Route::post('/update_compoff', [DisplayCompoffs::class, 'update_compoff']);

// Compoff Approval
// Compoff Approval
Route::prefix('compoff')->group(function () {
    Route::post('/approve/{id}', [CompoffApproval::class, 'approve'])->name('compoff_approve');
    Route::post('/approve_multiple', [CompoffApproval::class, 'approveMultiple'])->name('compoff_approve_multiple');
    Route::post('/reject/{id}', [CompoffApproval::class, 'reject'])->name('compoff_reject');
    Route::post('/reject_multiple', [CompoffApproval::class, 'rejectMultiple'])->name('compoff_reject_multiple');
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