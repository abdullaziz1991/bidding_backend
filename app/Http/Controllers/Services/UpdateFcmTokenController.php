<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UpdateFcmTokenController extends Controller
{
    public function updateFcmToken(Request $request): \Illuminate\Http\JsonResponse
    {
        // ✅ التحقق من وجود الحقول المطلوبة
        $validated = $request->validate([
            'id' => 'required|integer|exists:users,id',
            'userFcmToken' => 'required|string',
        ]);

        try {
            // ✅ البحث عن المستخدم وتحديث التوكن
            $user = User::where('id', $validated['id'])->first();

            if ($user) {
                $user->userFcmToken = $validated['userFcmToken'];
                $user->save();

                return response()->json(['status' => 'Token updated successfully']);
            } else {
                return response()->json(['status' => 'User not found'], 404);
            }
        } catch (\Exception $e) {
            // ✅ التعامل مع الخطأ وتسجيله
            Log::error('FCM token update error: ' . $e->getMessage());
            return response()->json(['status' => 'Error updating token', 'error' => $e->getMessage()], 500);
        }
    }
}
