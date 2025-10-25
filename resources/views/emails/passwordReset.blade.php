<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>إعادة تعيين كلمة المرور</title>
</head>

<body>
    <h2>مرحباً {{ $user->userName }}</h2>
    <p>لقد طلبت إعادة تعيين كلمة المرور. اضغط على الرابط أدناه:</p>
    <p>
        <a href="{{ $resetUrl }}">إعادة تعيين كلمة المرور</a>
    </p>
    <p>إذا لم تطلب ذلك، فلا حاجة لاتخاذ أي إجراء.</p>
</body>

</html>