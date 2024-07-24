<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeMessages;
use App\Models\AdminMessages;
use App\Models\Attendance_Master as Attendance;
use Illuminate\Support\Facades\Log;

class EmployeeMessageController extends Controller
{

    public function displayMessages(Request $request)
    {
        try {
            $messages = EmployeeMessages::with(['attendance', 'sender', 'receiver'])->get();

            $data = $messages->map(function ($message) {
                return [
                    'employee_message_id' => $message->employee_message_id,
                    'attendance_id' => $message->attendance_id,
                    'attendance_date' => optional($message->attendance)->date,
                    'message_from' => $message->message_from,
                    'message_from_name' => optional($message->sender)->name,
                    'message_to' => $message->message_to,
                    'message_to_name' => optional($message->receiver)->name,
                    'parent_message' => $message->parent_id,
                    'message' => $message->message,
                    'message_status' => $message->message_status,
                ];
            });

            return response()->json(['status' => 200, 'messages' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to retrieve messages.', 'error' => $e->getMessage()], 500);
        }
    }

    public function sendReply(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendance_masters,attendance_id',
            'parent_id' => 'required|exists:admin_messages,message_id', // Assuming the primary key in admin_messages is 'id'
            'message' => 'required|string|max:255',
            'message_from' => 'required|exists:employees,employee_id',
            'message_to' => 'required|exists:employees,employee_id',
        ]);
    
        try {

            // Save message in employee_messages table
            $employeeMessage = new EmployeeMessages();
            $employeeMessage->attendance_id = $request->attendance_id;
            $employeeMessage->parent_id = $request->parent_id;
            $employeeMessage->message = $request->message;
            $employeeMessage->message_from = $request->message_from;
            $employeeMessage->message_to = $request->message_to; // Ensure this field is available
            $employeeMessage->message_status = 1;
            $employeeMessage->save();
    
            // Save message in admin_messages table with parent_id being the employee_message_id
            $adminMessage = new AdminMessages();
            $adminMessage->attendance_id = $request->attendance_id;
            $adminMessage->parent_id = $employeeMessage->employee_message_id; // Reference the employee_message_id
            $adminMessage->message = $request->message;
            $adminMessage->message_from = $request->message_from;
            $adminMessage->message_to = $request->message_to; // Ensure this field is available
            $adminMessage->message_status = 1;
            $adminMessage->save();
    
            return response()->json(['status' => 200, 'message' => 'Reply sent successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to send reply: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Failed to send reply.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    public function updateMessageStatus(Request $request)
    {
        $request->validate([
            'employee_message_id' => 'required|exists:employee_messages,employee_message_id',
            'message_status' => 'required|int|in:0,1,2',
        ]);

        try {
            $message = EmployeeMessages::findOrFail($request->employee_message_id);
            $message->message_status = $request->message_status;
            $message->save();

            return response()->json(['status' => 200, 'message' => 'Message status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to update message status.', 'error' => $e->getMessage()], 500);
        }
    }

    public function clearMessages(Request $request)
    {
        $request->validate([
            'message_to' => 'required|exists:employees,employee_id',
        ]);

        try {
            $messages = EmployeeMessages::where('message_to', $request->message_to)->get();

            foreach ($messages as $message) {
                $message->message_status = 0;
                $message->save();
            }

            return response()->json(['status' => 200, 'message' => 'Messages cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to clear messages.', 'error' => $e->getMessage()], 500);
        }
    }

    public function displayThreadMessages($parentId)
    {
        try {
            $threadMessages = EmployeeMessages::where('parent_id', $parentId)
                ->join('attendance_masters', 'employee_messages.attendance_id', '=', 'attendance_masters.attendance_id')
                ->select(
                    'employee_messages.*',
                    'attendance_masters.date as attendance_date'
                )
                ->orderBy('attendance_date', 'asc')
                ->orderBy('employee_messages.created_at', 'asc')
                ->get();
    
            $data = $threadMessages->map(function ($message) {
                return [
                    'employee_message_id' => $message->message_id,
                    'message_from' => $message->message_from,
                    'message_from_name' => optional($message->sender)->name,
                    'message_to' => $message->message_to,
                    'message_to_name' => optional($message->receiver)->name,
                    'parent_message' => $message->parent_id,
                    'attendance_date' => $message->attendance_date, // Include attendance date
                    'message' => $message->message,
                    'message_status' => $message->message_status,
                    'created_at' => $message->created_at, // Include created_at
                ];
            });
    
            return response()->json(['status' => 200, 'thread_messages' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to retrieve thread messages.', 'error' => $e->getMessage()], 500);
        }
    }
    
}
