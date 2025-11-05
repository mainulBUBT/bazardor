<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 14px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .otp-section {
            background-color: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .otp-label {
            font-size: 12px;
            color: #999999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            letter-spacing: 4px;
            text-align: center;
            font-family: 'Courier New', monospace;
        }
        .expiry {
            font-size: 12px;
            color: #e74c3c;
            text-align: center;
            margin-top: 10px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            font-size: 13px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 0;
            font-size: 12px;
            color: #999999;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            background-color: #667eea;
            color: #ffffff;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $user->first_name }},
            </div>

            <div class="message">
                We received a request to reset your password. Use the OTP code below to reset your password. This code is valid for 15 minutes.
            </div>

            <div class="otp-section">
                <div class="otp-label">Your OTP Code</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="expiry">Expires in 15 minutes</div>
            </div>

            <div class="warning">
                <strong>⚠️ Security Notice:</strong> Never share this OTP with anyone. Our team will never ask for your OTP code.
            </div>

            <div class="message">
                If you did not request a password reset, please ignore this email or contact our support team immediately.
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.frontend_url') }}/reset-password" class="button">Reset Password</a>
            </div>
        </div>

        <div class="footer">
            <p>
                This is an automated email. Please do not reply to this message.<br>
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
