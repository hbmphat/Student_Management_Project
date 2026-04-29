<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu | ENGBREAK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-side {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-side {
            height: 100vh;
           background: linear-gradient(135deg, rgba(13, 110, 253, 0.9), rgba(10, 88, 202, 0.8)), url('https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2029&auto=format&fit=crop') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            padding: 4rem;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            color: #0d6efd;
            font-weight: bold;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
        }

        .btn-reset {
            padding: 14px;
            font-weight: bold;
            border-radius: 8px;
            background-color: #0d6efd;
            border: none;
        }
    </style>
</head>

<body>

    <div class="row g-0">
        <div class="col-lg-6 d-none d-lg-flex image-side">
            <h1 class="fw-bold display-4 mb-4"><i class="fas fa-shield-alt"></i> Bảo mật tài khoản</h1>
            <h3 class="fw-light mb-4">Khôi phục quyền truy cập</h3>
            <p class="lead" style="opacity: 0.9;">Chúng tôi sẽ xác thực thông tin nhân viên của bạn trước khi cấp lại
                mật khẩu mới qua Email.</p>
        </div>

        <div class="col-lg-6 login-side bg-white">
            <div class="w-100" style="max-width: 420px; padding: 20px;">
                <h3 class="fw-bold text-dark mb-1">Quên mật khẩu?</h3>
                <p class="text-muted mb-4">Nhập thông tin đăng ký để nhận mật khẩu mới.</p>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-floating mb-4">
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                            id="username" name="username" placeholder="Tên đăng nhập" required autofocus>
                        <label for="username"><i class="fas fa-user me-2"></i>Tên đăng nhập</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="text" class="form-control @error('registration_code') is-invalid @enderror"
                            id="registration_code" name="registration_code" placeholder="Mã nhân viên (Mã đăng ký)"
                            required>
                        <label for="registration_code"><i class="fas fa-key me-2"></i>Mã nhân viên đã đăng ký</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-reset text-white shadow-sm mb-4">
                        XÁC THỰC & GỬI MẬT KHẨU <i class="fas fa-paper-plane ms-2"></i>
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-muted small text-decoration-none fw-bold">
                            <i class="fas fa-chevron-left me-1"></i> Quay lại đăng nhập
                        </a>
                    </div>
                </form>
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
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        };

        @if (session('status'))
            showToast("{{ session('status') }}", 'success', 'Thành công');
        @endif

        @if (session('error'))
            showToast("{{ session('error') }}", 'error', 'Lỗi');
        @endif

        @if ($errors->has('username') || $errors->has('registration_code'))
            showToast("{{ $errors->first('username') ?? $errors->first('registration_code') ?? 'Thông tin xác thực không chính xác!' }}", 'error', 'Xác thực thất bại');
        @endif
    </script>

</body>

</html>
