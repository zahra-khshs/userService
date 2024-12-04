<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body>
    <h1>تأیید ایمیل</h1>
    <p>سلام {{ $user->name }},</p>
    <p>برای تأیید ایمیل خود، لطفاً بر روی لینک زیر کلیک کنید:</p>
    <a href="{{ route('verify.email', $user->email_verification_token) }}">تأیید ایمیل</a>
    <p>با تشکر!</p>
</body>
</html>
