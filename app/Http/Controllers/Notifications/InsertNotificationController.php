// <?php

// namespace App\Http\Controllers\Insert;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;


// class InsertNotificationController extends Controller
// {
//     // إضافة إشعار جديد
//     public function store(Request $request)
//     {
//         $request->validate([
//             'user_id' => 'required|exists:users,id', // لازم يكون موجود
//             'content' => 'required|string',
//         ]);

//         $notification = Notification::create([
//             'user_id' => $request->user_id,
//             'content' => $request->content,
//         ]);

//         return response()->json([
//             'message' => 'تمت إضافة الإشعار بنجاح',
//             'data'    => $notification,
//         ], 201);
//     }
// }