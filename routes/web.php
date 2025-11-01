<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\NotificationService;

Route::get('/reset-password-form', function () {
    return view('reset-password-form');
});

Route::get('/password-reset-success', function () {
    return view('password_reset_success');
});


Route::post('/submit-reset-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'token' => 'required',
        'password' => 'required|confirmed|min:6',
    ]);

    $reset = DB::table('password_resets')
        ->where('email', $request->email)
        ->first();

    if (!$reset || !Hash::check($request->token, $reset->token)) {
        return response()->json(['message' => 'رمز التحقق غير صالح أو منتهي.'], 400);
    }

    // تغيير كلمة المرور
    $user = User::where('userEmail', $request->email)->first();
    if (!$user) {
        return response()->json(['message' => 'المستخدم غير موجود.'], 404);
    }

    $user->userPassword = Hash::make($request->password);

    $user->save();

    DB::table('password_resets')->where('email', $request->email)->delete();
    
     $user->tokens()->delete();
    try {
        
          $notificationService = new NotificationService();
       $notificationService->addNotification(
       $user->id,
       "Password has been changed",
       "Successfully changed your password"
        );
   
    Log::info("✅ تم إدخال إشعار جديد للمستخدم ID: {$user->id}", $newNotification);
} catch (\Exception $e) {
    Log::error("❌ فشل إدخال الإشعار: " . $e->getMessage());
}

return redirect('/password-reset-success');
});
