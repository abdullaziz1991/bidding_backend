<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



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
        $row = DB::table('notifications')
            ->where('user_id', $userId)
            ->first();

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