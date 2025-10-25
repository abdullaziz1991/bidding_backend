<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;


 
class RegisterController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/RegisterUser",
 *     summary="تسجيل مستخدم جديد",
 *     description="يقوم بتسجيل مستخدم جديد ويتحقق من نسخة التطبيق",
 *     operationId="registerUser",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"userName","userEmail","userPassword","userGender","userNumber","appVersion"},
 *             @OA\Property(property="userName", type="string", example="ahmad"),
 *             @OA\Property(property="userEmail", type="string", example="ahmad@example.com"),
 *             @OA\Property(property="userPassword", type="string", example="password123"),
 *             @OA\Property(property="userGender", type="string", example="Male"),
 *             @OA\Property(property="userNumber", type="string", example="+963990000000"),
 *             @OA\Property(property="userFcmToken", type="string", example="fcm_token_optional"),
 *             @OA\Property(property="appVersion", type="string", example="1.0.0")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="تم تسجيل المستخدم بنجاح"
 *     ),
 *     @OA\Response(
 *         response=426,
 *         description="يجب تحديث التطبيق"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="خطأ داخلي أثناء التسجيل"
 *     )
 * )
 */
    public function registerUser(RegisterRequest $request): JsonResponse
    {
        try {
            // 🔹 تحميل ملف نسخة التطبيق
            $versionFilePath = 'json/AppVersion.json';

            if (!Storage::disk('public')->exists($versionFilePath)) {
                return response()->json(['message' => 'App version file not found.'], 500);
            }

            $versionContent = Storage::disk('public')->get($versionFilePath);
            $versionData = json_decode($versionContent, true);

            if (!$versionData || !isset($versionData['latest_version']) || !isset($versionData['update_url'])) {
                return response()->json(['message' => 'Invalid version file format.'], 500);
            }

            // 🔹 تحقق من نسخة التطبيق
            $clientAppVersion = $request->input('appVersion');
            $latestVersion = $versionData['latest_version'];
            $updateUrl = $versionData['update_url'];

            if ($clientAppVersion !== $latestVersion) {
                return response()->json([
                    'message' => 'يرجى تحميل النسخة الجديدة من التطبيق.',
                    'update_url' => $updateUrl,
                ], 426); // 426 Upgrade Required
            }

            // ✅ إنشاء المستخدم مع userFcmToken إن وُجد
            $user = User::create([
                'userName' => $request->userName ?? 'user',
                'userEmail' => $request->userEmail,
                'userPassword' => Hash::make($request->userPassword),
                'userGender' => $request->userGender ?? 'Male',
                'userNumber' => $request->userNumber ?? '+963990000000',
                'userFcmToken' => $request->userFcmToken ?? null, // 🔸 هنا الإضافة
            ]);

            // 🔹 إنشاء التوكن
            $token = $user->createToken('auth_token')->plainTextToken;

          DB::table('notifications')->insert([
             'user_id'   =>  $user->id,
             'content'   => json_encode([]), 
                  ]);



            return response()->json([
                'message' => 'User registered successfully.',
                'access_token' => $token,
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء عملية التسجيل.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
