<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SignOutController extends Controller
{
    public function signOut(Request $request)
    {
        try {
            $userId = $request->input('userId');

            if (! $userId) {
                return response()->json(['message' => 'userId مفقود'], 400);
            }

            $user = User::find($userId);

            if (! $user) {
                return response()->json(['message' => 'المستخدم غير موجود'], 404);
            }
            if ($user->tokens()->exists()) {
                $user->tokens()->delete();
                Log::info('تم تسجيل الخروج وحذف التوكينات', ['user_id' => $user->id]);
            } else {
                Log::warning('لم يتم العثور على أي توكنات للمستخدم', ['user_id' => $user->id]);
            }

            return response()->json(['message' => 'تم تسجيل الخروج بنجاح.']);
        } catch (\Throwable $e) {
            Log::error('SignOut Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'حدث خطأ أثناء تسجيل الخروج',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}