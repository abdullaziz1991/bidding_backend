<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\PasswordResetMail;
use App\Services\NotificationService; 
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    
    //     protected $notificationService;

    // public function __construct(NotificationService $notificationService)
    // {
    //     $this->notificationService = $notificationService;
    // }

    
    public function sendResetLink(Request $request)
    {
        
        
        
        $request->validate(['userEmail' => 'required|email']);

        $user = User::where('userEmail', $request->userEmail)->first();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود.'], 404);
        }

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->userEmail],
            ['token' => Hash::make($token), 'created_at' => Carbon::now()]
        );

        $resetUrl = url("/reset-password-form?token=$token&email=" . urlencode($user->userEmail));

        Mail::to($user->userEmail)->send(new PasswordResetMail($user, $resetUrl));

        return response()->json(['message' => 'تم إرسال رابط إعادة تعيين كلمة المرور.']);
    }
  
  
//   public function submitResetPassword(Request $request)
// {
//     Log::info('🚀 دخل على submitResetPassword'); 

//     $request->validate([
//         'token' => 'required',
//         'email' => 'required|email',
//         'password' => 'required|confirmed|min:6',
//     ]);


//     $resetRecord = DB::table('password_resets')
//             ->where('email', $request->email)
//             ->first();

//     if (!$resetRecord) {
//         return back()->withErrors(['email' => 'رابط إعادة التعيين غير صالح.']);
//     }

//     if (!Hash::check($request->token, $resetRecord->token)) {
//         return back()->withErrors(['token' => 'رابط إعادة التعيين غير صالح أو منتهي الصلاحية.']);
//     }

//     $user = User::where('userEmail', $request->email)->first();

//     if (!$user) {
//         return back()->withErrors(['email' => 'المستخدم غير موجود.']);
//     }

//     // تحديث كلمة المرور
//     $user->userPassword = Hash::make($request->password);
//     $user->save();

//     // حذف سجل إعادة التعيين
//     DB::table('password_resets')->where('email', $request->email)->delete();
    
    


//     return view('password_reset_success');
// }
}

