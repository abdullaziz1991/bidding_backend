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

class PasswordResetController extends Controller
{
    
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
}

