 <?php
  require_once 'FirebaseConfig.php'; // استدعاء المفتاح
  header('Content-Type: application/json');

  // ✅ استقبال البيانات بصيغة JSON
  $inputJSON = file_get_contents("php://input");
  $data = json_decode($inputJSON, true);

  $userEmail = $data['userEmail'] ?? '';
  $userPassword = $data['userPassword'] ?? '';

  if (empty($userEmail) || empty($userPassword)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit;
  }

  // 🔹 دالة لإرسال الطلب إلى Firebase
  function sendFirebaseRequest($url, $data)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['response' => json_decode($response, true), 'http_code' => $http_code];
  }

  // 🔹 محاولة تسجيل الدخول أولاً
  $login_url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=" . FIREBASE_API_KEY;
  $firebase_data = [
    'email' => $userEmail,
    'password' => $userPassword,
    'returnSecureToken' => true
  ];

  $login_result = sendFirebaseRequest($login_url, $firebase_data);

  if ($login_result['http_code'] == 200 && isset($login_result['response']['idToken'])) {
    // ✅ تسجيل الدخول ناجح
    echo json_encode([
      'status' => 'success',
      'message' => 'User logged in successfully',
      'idToken' => $login_result['response']['idToken'],
      'refreshToken' => $login_result['response']['refreshToken'],
      'expiresIn' => $login_result['response']['expiresIn'],
      'localId' => $login_result['response']['localId']
    ]);
    http_response_code(200);
  } else {
    // 🔹 إذا فشل تسجيل الدخول، نحاول تسجيل المستخدم لأول مرة
    $register_url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=" . FIREBASE_API_KEY;
    $register_result = sendFirebaseRequest($register_url, $firebase_data);

    if ($register_result['http_code'] == 200 && isset($register_result['response']['idToken'])) {
      // ✅ تم تسجيل المستخدم بنجاح
      echo json_encode([
        'status' => 'success',
        'message' => 'User registered successfully',
        'idToken' => $register_result['response']['idToken'],
        'refreshToken' => $register_result['response']['refreshToken'],
        'expiresIn' => $register_result['response']['expiresIn'],
        'localId' => $register_result['response']['localId']
      ]);
      http_response_code(200);
    } else {
      // ❌ فشل التسجيل وإرجاع الخطأ
      http_response_code(401);
      echo json_encode([
        'status' => 'error',
        'message' => $register_result['response']['error']['message'] ?? 'Authentication failed',
        'firebase_response' => $register_result['response']
      ]);
    }
  }



















