<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UpdateNotificationStatus extends Controller
{
    public function markAsRead(Request $request)
    {
        $id = $request->input('id');        // ID السطر في جدول notifications
        $date = $request->input('date');    // تاريخ الإشعار

        // جلب السطر
        $notification = DB::table('notifications')->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                "status" => false,
                "message" => "Notification not found"
            ], 404);
        }

        // فك JSON المخزن
        $content = json_decode($notification->content, true);

        if (!is_array($content)) {
            return response()->json([
                "status" => false,
                "message" => "Invalid content format"
            ], 400);
        }

        // البحث عن العنصر الذي يطابق التاريخ
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

        // تحديث في قاعدة البيانات
        DB::table('notifications')->where('id', $id)->update([
            'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ]);

        return response()->json([
            "status" => true,
            "message" => "Notification updated successfully",
            // "updatedContent" => $content
        ]);
    }
}


// namespace App\Http\Controllers\Notifications;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use App\Http\Controllers\Controller;
      
// class UpdateNotificationStatus extends Controller
// {
//     public function markAsRead(Request $request)
//     {
//         $id = $request->input('id');        // ID السطر في جدول notifications
//         $index = $request->input('itemIndex');  // موقع العنصر داخل content

//         // جلب السطر
//         $notification = DB::table('notifications')->where('id', $id)->first();

//         if (!$notification) {
//             return response()->json([
//                 "status" => false,
//                 "message" => "Notification not found"
//             ], 404);
//         }

//         // فك JSON المخزن
//         $content = json_decode($notification->content, true);

//         if (!is_array($content) || !isset($content[$index])) {
//             return response()->json([
//                 "status" => false,
//                 "message" => "Invalid index"
//             ], 400);
//         }

//         // تعديل isRead للعنصر المطلوب
//         $content[$index]['isRead'] = true;

//         // تحديث في قاعدة البيانات
//         DB::table('notifications')->where('id', $id)->update([
//             'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
//         ]);

//         return response()->json([
//             "status" => true,
//             "message" => "Notification updated successfully",
//             "updatedContent" => $content
//         ]);
//     }
// }
