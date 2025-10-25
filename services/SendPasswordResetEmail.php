<?php
require_once 'FirebaseConfig.php'; // استدعاء المفتاح
header('Content-Type: application/json');

// ✅ استقبال البيانات بصيغة JSON
$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

$userEmail = $data['userEmail'] ?? '';

if (empty($userEmail)) {
  http_response_code(400);
  echo json_encode(['status' => 'error', 'message' => 'Email is required.']);
  exit;
}

// 🔹 إعداد بيانات الطلب لإعادة تعيين كلمة المرور
$firebase_url = "https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key=" . FIREBASE_API_KEY;
$firebase_data = json_encode([
  'requestType' => 'PASSWORD_RESET',
  'email' => $userEmail
]);

// 🔹 إرسال الطلب إلى Firebase باستخدام cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $firebase_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $firebase_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

// 🔹 التحقق من استجابة Firebase
if ($http_code == 200) {
  echo json_encode(['status' => 'success', 'message' => 'Password reset email sent successfully.']);
  http_response_code(200);
} else {
  http_response_code(400);
  echo json_encode([
    'status' => 'error',
    'message' => $result['error']['message'] ?? 'Failed to send password reset email.',
    'http_code' => $http_code
  ]);
}
