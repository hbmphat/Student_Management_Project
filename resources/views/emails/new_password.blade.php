<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mật khẩu mới</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f7fb;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
        }
        .wrapper {
            max-width: 640px;
            margin: 0 auto;
            padding: 32px 16px;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #0d6efd, #0052cc);
            color: #ffffff;
            padding: 28px 32px;
        }
        .content {
            padding: 32px;
            line-height: 1.7;
            font-size: 15px;
        }
        .password-box {
            background: #eef6ff;
            border: 1px dashed #0d6efd;
            border-radius: 12px;
            padding: 18px 20px;
            margin: 24px 0;
            text-align: center;
        }
        .password {
            display: inline-block;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #0b5ed7;
            word-break: break-word;
        }
        .footer {
            padding: 0 32px 32px;
            color: #6b7280;
            font-size: 13px;
        }
        .note {
            margin-top: 20px;
            padding: 14px 16px;
            border-left: 4px solid #f59e0b;
            background: #fff8e6;
            border-radius: 8px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h2 style="margin: 0; font-size: 22px;">ENGBREAK</h2>
                <p style="margin: 8px 0 0; opacity: 0.95;">Mật khẩu mới cho tài khoản của bạn</p>
            </div>
            <div class="content">
                <p>Xin chào <strong>{{ $name }}</strong>,</p>
                <p>Bạn vừa gửi yêu cầu xác thực quên mật khẩu. Hệ thống đã tạo một mật khẩu mới cho tài khoản của bạn.</p>

                <div class="password-box">
                    <div style="margin-bottom: 8px; color: #475569; font-size: 13px;">Mật khẩu mới của bạn</div>
                    <div class="password">{{ $password }}</div>
                </div>

                <p>Hãy đăng nhập lại bằng mật khẩu này và đổi sang mật khẩu riêng của bạn sau khi vào hệ thống.</p>

                <div class="note">
                    Nếu bạn không yêu cầu thao tác này, vui lòng liên hệ quản trị viên để được hỗ trợ.
                </div>
            </div>
            <div class="footer">
                Email này được gửi tự động từ hệ thống ENGBREAK.
            </div>
        </div>
    </div>
</body>
</html>
