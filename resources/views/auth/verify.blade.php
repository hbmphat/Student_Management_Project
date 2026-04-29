<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email | ENGBREAK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .verification-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 50px;
            max-width: 500px;
            text-align: center;
        }

        .verification-icon {
            font-size: 4rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }

        .verification-card h2 {
            color: #333;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .verification-card p {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn-verify {
            padding: 12px 30px;
            font-weight: bold;
            border-radius: 8px;
            margin: 10px 5px;
        }

        .btn-resend {
            background-color: #28a745;
            border: none;
        }

        .btn-resend:hover {
            background-color: #218838;
        }

        .btn-back {
            background-color: #6c757d;
            border: none;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: left;
        }

        .info-box strong {
            color: #0d6efd;
        }
    </style>
</head>

<body>

    <div class="verification-container">
        <div class="verification-card">
            <div class="verification-icon">
                <i class="fas fa-envelope-open-text"></i>
            </div>

            <h2>Xác thực Email của bạn</h2>
            <p>Trước khi tiếp tục, vui lòng kiểm tra email để xác thực địa chỉ của bạn.</p>

            <div class="info-box">
                <strong><i class="fas fa-info-circle me-2"></i>Hướng dẫn:</strong>
                <p class="mb-0">Chúng tôi đã gửi một liên kết xác thực đến email của bạn. Nhấp vào liên kết đó để hoàn tất quá trình xác thực.</p>
            </div>

            <p class="text-muted small">
                Không nhận được email? <br>
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}" id="resendForm">
                    @csrf
                    <button type="button" class="btn btn-link p-0 text-primary fw-bold" onclick="document.getElementById('resendForm').submit();">
                        <i class="fas fa-redo me-1"></i>Gửi lại email xác thực
                    </button>
                </form>
            </p>

            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-secondary btn-verify btn-back">
                    <i class="fas fa-home me-2"></i>Quay về trang chủ
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.showToast = function(message, icon = 'success', title = 'Thông báo') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: title,
                text: message,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        };

        @if (session('resent'))
            showToast('Email xác thực đã được gửi lại!', 'success', 'Thành công');
        @endif

        @if (session('status'))
            showToast("{{ session('status') }}", 'success', 'Thông báo');
        @endif

        @if (session('error'))
            showToast("{{ session('error') }}", 'error', 'Lỗi');
        @endif
    </script>

</body>

</html>
