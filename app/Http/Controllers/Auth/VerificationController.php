<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;


class VerificationController extends Controller
{
    public function sendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('userEmail', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // إنشاء رابط التوثيق
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(30),
            ['id' => $user->id, 'hash' => sha1($user->userEmail)]
        );

        // إرسال الإيميل
        Mail::to($user->userEmail)->send(new VerifyEmail($verificationUrl));

        return response()->json([
            'message' => 'Verification link sent to email',
            // 'link' => $verificationUrl
        ]);
    }

public function verify(Request $request, $id, $hash)
{
    $user = User::findOrFail($id);

    if (sha1($user->userEmail) !== $hash) {
        return response()->json(['error' => 'Invalid verification link'], 400);
    }


    //     // ✅ استدعاء ملف الخدمة
    //      require_once base_path('services/Send_Email_Verification_Notification.php');
    //     // ✅ استدعاء الدالة وتمرير الـ userId

    // sendPushNotification(
    // $user->userFcmToken, "تفعيل البريد الإلكتروني","تم تفعيل بريدك الإلكتروني بنجاح، اضغط على هذا الإشعار لإتمام التفعيل");

   
      
      if (!$user->email_verified_at) {
    $user->email_verified_at = now();
    $user->save();

    $notificationService = new NotificationService();
    $notificationService->addNotification(
        $user->id,
        "Email Verified",
        "Your email has been successfully verified"
    );
}

   $user->tokens()->delete();

    return view('verify_success'); // 👈 يرجع الصفحة الجميلة
}

}
