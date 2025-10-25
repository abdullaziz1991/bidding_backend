<?php

function validate_required_fields(array $data, array $required_fields)
{
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            $missing_fields[$field] = 'Missing field';
        } elseif (empty($data[$field]) && $data[$field] !== "0") {
            $missing_fields[$field] = 'Empty value';
        }
    }

    if (!empty($missing_fields)) {
        http_response_code(400);
        echo json_encode([
            'status' => "Missing or invalid parameters",
            'missingFields' => $missing_fields
        ]);
        exit;
    }
}

//  ✅ كود التحقق من القيم الناقصة ويعيد القيم الناقصة بالاستجابة
// require_once __DIR__ . '/../Services/Check_for_Missing_Values.php';
// $required_fields = ["userName", "userEmail", "userPassword", "userGender", "userImage", "userNumber", "userToken"];
// validate_required_fields($data, $required_fields);
