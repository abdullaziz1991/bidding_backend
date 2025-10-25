<?php


namespace App\Http\Controllers\Auth;

require_once '/home/u169611441/domains/abdullaziz.online/public_html/Biddings/services/unchanged/vendor/autoload.php';

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Google\Client;
use Illuminate\Support\Facades\Storage;

class SingInWithGoogleController extends Controller
{
    public function googleSignIn(Request $request)
    {
        $idToken = $request->input('idToken');
        $userFcmToken = $request->input('userFcmToken');
        
        // 🔹 تحقق من إصدار التطبيق
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

        // ✅ أنشئ كائن Google\Client
        $client = new Client();
        $client->setClientId('112927762370-fnjhh785tj7mtesp3p7gaeigsql1msej.apps.googleusercontent.com');

        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            $email = $payload['email'];
            $name = $payload['name'];

            $user = User::where('userEmail', $email)->first();

            if (!$user) {
                // 🔹 إنشاء مستخدم جديد
                $user = User::create([
                    'userName' => $name,
                    'userEmail' => $email,
                    'userNumber' => '+963900000000', // قيمة مؤقتة
                    'userPassword' => bcrypt(uniqid()),
                    'userFcmToken' => $userFcmToken
                ]);
            }

            // 🔹 تحديث FCM Token إذا وُجد
            if ($request->has('userFcmToken')) {
                $user->userFcmToken = $request->userFcmToken;
                $user->save();
            }

            // 🔹 إنشاء التوكن
            $token = $user->createToken("api-token")->plainTextToken;

            return response()->json([
                'status' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        } else {
            return response()->json(['status' => false, 'message' => 'Invalid ID token'], 401);
        }
    }
}







// namespace App\Http\Controllers\Auth;

// require_once '/home/u169611441/domains/abdullaziz.online/public_html/Biddings/services/unchanged/vendor/autoload.php';

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\User;
// use Google\Client;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;
// use Exception;

// class SingInWithGoogleController extends Controller
// {
//     public function googleSignIn(Request $request)
//     {
//         try {
//             $idToken = $request->input('idToken');
//             $userFcmToken = $request->input('userFcmToken');

//             // ✅ تحقق من إصدار التطبيق
//             $versionFilePath = 'json/AppVersion.json';
//             if (!Storage::disk('public')->exists($versionFilePath)) {
//                 return response()->json(['message' => 'Version file not found.'], 500);
//             }

//             $versionContent = Storage::disk('public')->get($versionFilePath);
//             $versionData = json_decode($versionContent, true);

//             if (!isset($versionData['latest_version'], $versionData['update_url'])) {
//                 return response()->json(['message' => 'Invalid version file format.'], 500);
//             }

//             $latestVersion = $versionData['latest_version'];
//             $updateUrl = $versionData['update_url'];

//             if ($request->has('appVersion') && $request->appVersion !== $latestVersion) {
//                 return response()->json([
//                     'message' => 'Please update your app to continue.',
//                     'update_url' => $updateUrl,
//                 ], 426);
//             }

//             // ✅ تحقق من التوكن عبر Google
//             $client = new Client();
//             $client->setClientId('112927762370-fnjhh785tj7mtesp3p7gaeigsql1msej.apps.googleusercontent.com');
//             $payload = $client->verifyIdToken($idToken);

//             if (!$payload) {
//                 return response()->json(['status' => false, 'message' => 'Invalid ID token'], 401);
//             }

//             $email = $payload['email'];
//             $name = $payload['name'];
//             $pictureUrl = $payload['picture'] ?? null;

//             $user = User::where('userEmail', $email)->first();

//             if (!$user) {
//                 // ✅ تحميل صورة Google إن وُجدت
//                 $imageName = null;
// if ($pictureUrl) {
//     try {
//         $ch = curl_init($pictureUrl);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//         curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
//         $imageContent = curl_exec($ch);
//         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         curl_close($ch);

//         \Log::info("Downloaded image content size: " . strlen($imageContent));
//         if ($httpCode == 200 && $imageContent && strlen($imageContent) > 100) {
//             $extension = 'jpg';
//             $imageName = 'google_' . Str::random(10) . '.' . $extension;
//             $saved = Storage::disk('public')->put("personalImages/" . $imageName, $imageContent);

//             if ($saved && Storage::disk('public')->exists("personalImages/" . $imageName)) {
//                 \Log::info("Image saved successfully: " . $imageName);
//             } else {
//                 \Log::error("Failed to save image: " . $imageName);
//                 $imageName = null;
//             }
//         } else {
//             \Log::error("Failed to download image or image too small, HTTP code: $httpCode");
//             $imageName = null;
//         }
//     } catch (Exception $e) {
//         \Log::error("Exception while downloading image: " . $e->getMessage());
//         $imageName = null;
//     }
// }


//                 // ✅ إنشاء المستخدم
//                 $user = User::create([
//                     'userName' => $name,
//                     'userEmail' => $email,
//                     'userNumber' => '+963900000000',
//                     'userPassword' => bcrypt(uniqid()),
//                     'userFcmToken' => $userFcmToken,
//                     'userImage' => $imageName,
//                 ]);
//             } else {
//                 // ✅ تحديث FCM Token إن تم تغييره
//                 if ($request->has('userFcmToken')) {
//                     $user->userFcmToken = $userFcmToken;
//                     $user->save();
//                 }
//             }

//             // ✅ إنشاء التوكن
//             $token = $user->createToken("api-token")->plainTextToken;

//             return response()->json([
//                 'status' => true,
//                 'access_token' => $token,
//                 'token_type' => 'Bearer',
//                 'user' => $user,
//                 'image'=> $pictureUrl
//             ]);

//         } catch (Exception $e) {
//             // ✅ إرسال الخطأ لتطبيق Flutter
//             return response()->json([
//                 'status' => false,
//                 'message' => 'An error occurred during sign-in.',
//                 'error' => $e->getMessage(),
//             ], 500);
//         }
//     }
// }



