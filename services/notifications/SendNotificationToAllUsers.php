<?php
include '../database.php';

function sendNotificationToAllUsers($title, $body) {
    // عنوان URL الخاص بـ Firebase
    $url = 'https://fcm.googleapis.com/fcm/send';

    // مفتاح الخادم (Server Key) الخاص بمشروعك
    $serverKey = 'YOUR_SERVER_KEY_HERE';

    // بيانات الإشعار
    $notification = [
        'title' => $title,
        'body' => $body,
        'sound' => 'default'
    ];

    // الحمولة (Payload) لتحديد الإرسال إلى جميع المستخدمين
    $fields = [
        'to' => '/topics/all', // إرسال إلى جميع المشتركين في الموضوع "all"
        'notification' => $notification,
        'data' => [ // بيانات إضافية يمكن استخدامها
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'type' => 'general',
        ],
    ];

    // إعدادات الطلب HTTP
    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json'
    ];

    // تهيئة CURL
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    // تنفيذ الطلب
    $result = curl_exec($ch);

    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    // إغلاق CURL
    curl_close($ch);

    // عرض النتيجة
    echo $result;
}

// استدعاء الدالة لإرسال الإشعار
sendNotificationToAllUsers("عنوان الإشعار", "محتوى الإشعار");
