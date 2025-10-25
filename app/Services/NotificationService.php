<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function addNotification($userId, $title, $body, $data="", $isRead = false, $date = null)
    {
        $notificationRow = DB::table('notifications')
            ->where('user_id', $userId)
            ->first();


        $content = $notificationRow ? json_decode($notificationRow->content, true) ?? [] : [];

        $newNotification = [
            "title"  => $title,
            "body"   => $body,
            "data"   => $data,
            "isRead" => $isRead,
            "date"   => $date ?? now()->toISOString(),
        ];

        array_unshift($content, $newNotification);
        if ($notificationRow) {
            DB::table('notifications')
                ->where('id', $notificationRow->id)
                ->update([
                    'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                ]);
        } else {
            DB::table('notifications')->insert([
                'user_id' => $userId,
                'content' => json_encode([$newNotification], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]);
        }

        return $newNotification;
    }
}


// use App\Http\Controllers\Notifications\NotificationService;
//             $notificationService = new NotificationService();
// $notificationService->addNotification(
//     $userId,
//     'تم تغيير كلمة السر',
//     'لقد قمت بتغيير كلمة السر بنجاح'
// );






