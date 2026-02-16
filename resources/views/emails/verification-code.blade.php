<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification code</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #374151; margin: 0; padding: 0; background: #f3f4f6; }
        .wrapper { max-width: 400px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 32px; text-align: center; }
        .code { font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #4f46e5; margin: 24px 0; }
        .footer { font-size: 12px; color: #9ca3af; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <h1 style="margin: 0 0 8px 0; font-size: 18px;">Verify your email</h1>
            <p style="margin: 0; color: #6b7280;">Enter this code on the website to complete registration:</p>
            <div class="code">{{ $code }}</div>
            <p style="margin: 0; font-size: 14px; color: #6b7280;">This code expires in 15 minutes.</p>
            <div class="footer">If you didn't request this, you can ignore this email.</div>
        </div>
    </div>
</body>
</html>
