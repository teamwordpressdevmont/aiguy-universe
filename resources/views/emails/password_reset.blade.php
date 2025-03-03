<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <p>Click the button below to reset your password:</p>
        <a href="{{ $resetLink }}" class="btn">Reset Password</a>
        <p>If the button doesn't work, copy and paste this link into your browser:</p>
        <p>{{ $resetLink }}</p>
        <div class="footer">
            <p>If you did not request a password reset, ignore this email.</p>
        </div>
    </div>
</body>
</html>
