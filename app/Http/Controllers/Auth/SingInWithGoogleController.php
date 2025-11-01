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
              $versionFilePath = 'json/AppVersion.json';
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
        if ($request->has('appVersion') && $request->appVersion !== $latestVersion) {
            return response()->json([
                'message' => 'Please update your app to continue.',
                'update_url' => $updateUrl,
            ], 426); // 426 Upgrade Required
        }
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