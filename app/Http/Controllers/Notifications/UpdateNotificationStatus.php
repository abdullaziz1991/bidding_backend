<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UpdateNotificationStatus extends Controller
{
    public function markAsRead(Request $request)
    {
        $id = $request->input('id');    
        $date = $request->input('date');  

        $notification = DB::table('notifications')->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                "status" => false,
                "message" => "Notification not found"
            ], 404);
        }

        $content = json_decode($notification->content, true);

        if (!is_array($content)) {
            return response()->json([
                "status" => false,
                "message" => "Invalid content format"
            ], 400);
        }

        $found = false;
        foreach ($content as &$item) {
            if (isset($item['date']) && $item['date'] === $date) {
                $item['isRead'] = true;
                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json([
                "status" => false,
                "message" => "Notification with given date not found"
            ], 404);
        }
        DB::table('notifications')->where('id', $id)->update([
            'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ]);

        return response()->json([
            "status" => true,
            "message" => "Notification updated successfully",
        ]);
    }
}