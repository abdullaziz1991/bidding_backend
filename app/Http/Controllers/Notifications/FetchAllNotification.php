<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use App\Services\NotificationService;


class FetchAllNotification extends Controller
{
    public function fetch(Request $request)
    {
        $userId = $request->input('userId');

        if (!$userId) {
            return response()->json([
                "status" => false,
                "message" => "userId is required",
            ]);
        }

        // ✅ ابحث بالسطر المرتبط بالـ userId فقط
        $row = DB::table('notifications')
            ->where('user_id', $userId)
            ->first();
        
    //       $notificationService = new NotificationService();
    //   $notificationService->addNotification(
    //   3,
    //   "Password has been changed",
    //   "Successfully changed your password"
    //     );
   

        if ($row) {
            return response()->json([
                "status" => true,
                "notification" => [
                    "id" => $row->id,
                    "content" => $row->content, // يرجع النص كما هو
                ],
            ]);
        }
        
  

        return response()->json([
            "status" => false,
            "message" => "No notification found for this user",
        ]);
    }
}


// namespace App\Http\Controllers\Notifications;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;


// class FetchAllNotification extends Controller
// {
//     public function fetch(Request $request)
//     {


//         $userId = $request->input('userId');
//         $query = DB::table('notifications');

//         if ($userId) {
//             $query->where('user_id', $userId);
//         }

//         $rows = $query->get();

//         $notifications = $rows->map(function ($row) {
//             return [
//                 "id" => $row->id,
//                 "content" => $row->content, // إرجاع النص كما هو، بدون تعديل
//             ];
//         });
        

//         return response()->json([
//             "status" => true,
//             "notifications" => $notifications,
//         ]);
        

//     }
// }


