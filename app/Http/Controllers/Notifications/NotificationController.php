// <?php

// namespace App\Http\Controllers\Notifications;

// use App\Http\Controllers\Controller;
// use App\Services\NotificationService;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;

// class NotificationController extends Controller
// {
//     protected $notificationService;

//     public function __construct(NotificationService $notificationService)
//     {
//         $this->notificationService = $notificationService;
//     }

//     /**
//      * إضافة إشعار لمستخدم
//      */
//     public function add(Request $request)
//     {
//         try {
//             $request->validate([
//                 'user_id' => 'required|integer',
//                 'title'   => 'required|string|max:255',
//                 'body'    => 'required|string',
//             ]);

//             $userId = $request->input('user_id');
//             $title  = $request->input('title');
//             $body   = $request->input('body');

//             $newNotification = $this->notificationService->addNotification($userId, $title, $body);

//             Log::info("✅ تم إدخال إشعار جديد للمستخدم ID: {$userId}");

//             return response()->json([
//                 'status'  => true,
//                 'message' => 'تمت إضافة الإشعار بنجاح',
//                 'data'    => $newNotification,
//             ]);
//         } catch (\Exception $e) {
//             Log::error("❌ فشل إدخال الإشعار: " . $e->getMessage());

//             return response()->json([
//                 'status'  => false,
//                 'message' => 'فشل إدخال الإشعار',
//                 'error'   => $e->getMessage(),
//             ], 500);
//         }
//     }
// }
