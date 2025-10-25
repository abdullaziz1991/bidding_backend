<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Carbon\Carbon;
use App\Services\NotificationService; 

class BiddingService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory)
            ->withServiceAccount(base_path('app/Services/composer/service-account.json'))
            ->createMessaging();
    }

    public function processExpiredBiddings()
    {
        $currentTime = Carbon::now('Asia/Damascus')->format('Y-m-d\TH:i:s.u');

        $biddings = DB::table('biddings')
            ->where('biddingEndDate', '<=', $currentTime)
            ->where('biddingStatus', 1)
            ->get();

        foreach ($biddings as $row) {
            $this->handleBidding($row);
        }

        Log::info("✅ Biddings checked successfully at " . now());
    }

    private function handleBidding($row)
    {
        $productId = $row->product_id;
        $ownerId = $row->productOwnerId;
        $numberOfAlerts = $row->numberOfAlerts;
        $biddingDetails = json_decode($row->bidding_details, true);

        // لا يوجد مزايدات
        if (!is_array($biddingDetails) || count($biddingDetails) === 0) {
            return;
        }

        $highestBidderId = $biddingDetails[0]["bidder_id"];

        // إذا كان المالك هو نفسه الفائز
        if ($ownerId == $highestBidderId) {
            DB::table('biddings')
                ->where('product_id', $productId)
                ->update(['numberOfAlerts' => 3, 'biddingStatus' => 2]);

            return;
        }

        // تحديث عدد التنبيهات
        if ($numberOfAlerts < 1) {
            DB::table('biddings')
                ->where('product_id', $productId)
                ->update(['numberOfAlerts' => $numberOfAlerts + 1, 'biddingStatus' => 2]);
        } else {
            DB::table('biddings')
                ->where('product_id', $productId)
                ->update(['biddingStatus' => 2]);
        }

        // إشعار المالك
      
      

        Log::info("✅ Biddings checked successfully at " . now());
   
        Log::info("✅ ownerId is $ownerId" );
        Log::info("✅ highestBidderId is $highestBidderId" );
        
        $userOwner = DB::table('users')
        ->select('userFcmToken', 'userNumber')
        ->where('id', $ownerId)
        ->whereNotNull('userFcmToken')
        ->first();

        $userHighestBidder = DB::table('users')
        ->select('userFcmToken', 'userNumber')
       ->where('id', $highestBidderId)
       ->whereNotNull('userFcmToken')
        ->first();

        if (
       !$userOwner || 
       !$userHighestBidder || 
       empty($userOwner->userFcmToken) || 
       empty($userHighestBidder->userFcmToken)
      ) {
       Log::warning("⚠️ Missing token for owner_id: {$ownerId} or bidder_id: {$highestBidderId}");
       return;
       }

       $userOwnerNumber = $userOwner->userNumber;       
       $userOwnerFcmToken = $userOwner->userFcmToken;

       $userHighestBidderNumber = $userHighestBidder->userNumber;       
       $userHighestBidderFcmToken = $userHighestBidder->userFcmToken;

        
        
          $this->sendNotificationToUser( $ownerId, $userHighestBidderNumber, $userOwnerFcmToken, $productId, "انتهى وقت المزايدة", "لإكمال عملية التسليم، يرجى التنسيق مع الفائز بالمزاد على الرقم التالي : ","Bidding time is over","complete the delivery process");
 
        // $this->sendNotificationToUser($highestBidderId, $productId, "انتهى وقت المزايدة", "لإكمال عملية التسليم، يرجى التنسيق مع الفائز بالمزاد على الرقم التالي : ");

        // إشعار المزايد الفائز
        // $this->sendNotificationToUser($ownerId, $productId, "مبروك! لقد فزت بالمزاد", "لإتمام عملية التسليم، يُرجى التواصل مع مقدم العرض على الرقم التالي : ");
   
        $this->sendNotificationToUser($highestBidderId, $userOwnerNumber, $userHighestBidderFcmToken, $productId, "مبروك! لقد فزت بالمزاد", "لإتمام عملية التسليم، يُرجى التواصل مع مقدم العرض على الرقم التالي : ","Congratulations! You have won the auction","Please contact the presenter");
   
    }

    private function sendNotificationToUser($userId, $userNumber, $userFcmToken, $productId, $title, $body,$tableTitle,$tableBody)
    {
        try {
            // $user = DB::table('users')
            //     ->select('userFcmToken', 'userNumber')
            //     ->where('id', $userId)
            //     ->whereNotNull('userFcmToken')
            //     ->first();

            // if (!$user || empty($user->userFcmToken)) {
            //     Log::warning("⚠️ No token for user_id: {$userId}");
            //     return;
            // }

             $notificationService = new NotificationService();
             $notificationService->addNotification(
                $userId,
                $tableTitle,
                $tableBody,
                "$userNumber"
                );

            $message = [
                'token' => $userFcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => "$body $userNumber",
                ],
                'data' => [
                    'mobileNumber' =>(string) $userNumber,
                    'productId' =>(string) $productId,
                ],
                'android' => ['priority' => 'high'],
                'apns' => ['headers' => ['apns-priority' => '10']],
            ];

            $this->firebase->send($message);
        } catch (\Exception $e) {
            Log::error("🔥 FCM Error for user_id {$userId}: " . $e->getMessage());
        }
    }
}
