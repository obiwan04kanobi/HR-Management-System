<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance_Master as Attendance;
use App\Models\Messages;

class MessageController extends Controller
{
    public function displayMessages(Request $request)
    {
        try {
            // Fetch all messages with related attendance, sender, and receiver information
            $messages = Messages::with(['attendance', 'sender', 'receiver'])->get();

            // Format the response data
            $data = $messages->map(function ($message) {
                return [
                    'message_id' => $message->message_id,
                    'attendance_id' => $message->attendance_id,
                    'attendance_date' => optional($message->attendance)->date, // Use optional() to handle null relationships
                    'message_from' => $message->message_from,
                    'message_from_name' => optional($message->sender)->name,
                    'message_to' => $message->message_to,
                    'message_to_name' => optional($message->receiver)->name,
                    'message' => $message->message,
                    'message_status' => $message->message_status,
                ];
            });

            return response()->json(['status' => 200, 'messages' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to retrieve messages.', 'error' => $e->getMessage()], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'attendance_id' => 'required|exists:attendance_masters,attendance_id',
            'message_status' => 'required|boolean',
            'message' => 'required|string|max:255',
            'message_from' => 'required|exists:employees,employee_id',
        ]);

        try {
            // Find the attendance record
            $attendance = Attendance::findOrFail($request->attendance_id);
            
            // Get the employee_id from the attendance record
            $messageTo = $attendance->employee_id;

            // Create a new message
            $message = new Messages();
            $message->attendance_id = $request->attendance_id;
            $message->message_from = $request->message_from;
            $message->message_to = $messageTo;
            $message->message = $request->message;
            $message->message_status = $request->message_status;
            $message->save();

            return response()->json(['status' => 200, 'message' => 'Message sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to send message.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateMessageStatus(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,message_id',
            'message_status' => 'required|int:0,1,2',
        ]);
    
        try {
            // Find the message record and update the status
            $message = Messages::findOrFail($request->message_id);
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
            // Find all messages for the employee and clear them
            $messages = Messages::where('message_to', $request->message_to)->get();
    
            foreach ($messages as $message) {
                // $message->message = null; // Clear the message content (Uncomment this line if you want to clear the message content)
                $message->message_status = 0; // Mark message status as read
                $message->save();
            }
    
            return response()->json(['status' => 200, 'message' => 'Messages cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Failed to clear messages.', 'error' => $e->getMessage()], 500);
        }
    }
    
}
