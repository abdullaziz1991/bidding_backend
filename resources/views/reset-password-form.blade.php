<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>إعادة تعيين كلمة المرور</title>
</head>

<body>
    <h2>إعادة تعيين كلمة المرور</h2>

    <form method="POST" action="{{ url('/submit-reset-password') }}">
        @csrf
        <input type="hidden" name="token" value="{{ request()->query('token') }}">
        <input type="hidden" name="email" value="{{ request()->query('email') }}">

        <label for="password">كلمة المرور الجديدة:</label><br>
        <input type="password" name="password" required><br><br>

        <label for="password_confirmation">تأكيد كلمة المرور:</label><br>
        <input type="password" name="password_confirmation" required><br><br>

        <button type="submit">إعادة التعيين</button>
    </form>
</body>

</html>