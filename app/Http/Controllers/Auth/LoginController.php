<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Storage;


class LoginController extends Controller
{


    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $versionFilePath = 'json/AppVersion.json';

        // 🔹 التأكد من وجود الملف وقراءته
        if (!Storage::disk('public')->exists($versionFilePath)) {
            return response()->json(['message' => 'Version file not found.'], 500);
        }

        $versionContent = Storage::disk('public')->get($versionFilePath);
        $versionData = json_decode($versionContent, true);

        if (!isset($versionData['latest_version'], $versionData['update_url'])) {
            return response()->json(['message' => 'Invalid version file format.'], 500);
        }

        $latestVersion = $versionData['latest_version'];
        $updateUrl = $versionData['update_url'];

        // 🔹 مقارنة نسخة التطبيق
        if ($request->has('appVersion') && $request->appVersion !== $latestVersion) {
            return response()->json([
                'message' => 'Please update your app to continue.',
                'update_url' => $updateUrl,
            ], 426); // 426 Upgrade Required
        }

        // 🔹 التحقق من بيانات الدخول
        $user = User::where('userEmail', $request->userEmail)->first();

        if (! $user || ! Hash::check($request->userPassword, $user->userPassword)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // ✅ تحديث حقل userFcmToken إذا تم إرساله
        if ($request->has('userFcmToken')) {
            $user->userFcmToken = $request->userFcmToken;
            $user->save();
        }

        // 🔹 إنشاء التوكن
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}
