<?php
require_once 'FirebaseConfig.php'; // استدعاء المفتاح
function refreshIdToken($refreshToken)
{
 
  $url = 'https://securetoken.googleapis.com/v1/token?key=' . FIREBASE_API_KEY;

  $data = [
    'grant_type' => 'refresh_token',
    'refresh_token' => $refreshToken
  ];

  $headers = [
    'Content-Type: application/x-www-form-urlencoded'
  ];

  $options = [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => http_build_query($data)
  ];

  $ch = curl_init();
  curl_setopt_array($ch, $options);

  $response = curl_exec($ch);
  $err = curl_error($ch);
  curl_close($ch);

  if ($err) {
    return ['error' => true, 'message' => $err];
  }

  $decoded = json_decode($response, true);

  if (isset($decoded['id_token'])) {
    return [
      'success' => true,
      'idToken' => $decoded['id_token'],
      'refreshToken' => $decoded['refresh_token'], // يمكن استخدامه مرة أخرى
      'expiresIn' => $decoded['expires_in']
    ];
  } else {
    return ['error' => true, 'message' => $decoded];
  }
}

// مثال للاستخدام:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = json_decode(file_get_contents("php://input"), true);
  $refreshToken = $input['refreshToken'] ?? null;

  if ($refreshToken) {
    $result = refreshIdToken($refreshToken);
    header('Content-Type: application/json');
    echo json_encode($result);
  } else {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'refreshToken is required']);
  }
}
