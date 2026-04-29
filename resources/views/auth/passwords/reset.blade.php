<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu | ENGBREAK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .btn-reset:hover {
            background-color: #0b5ed7;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
            color: #6c757d;
            background: none;
            border: none;
            padding: 0;
        }

        .password-toggle:hover {
            color: #0d6efd;
        }
    </style>
</head>

<body>

    <div class="row g-0">
        <div class="col-lg-6 d-none d-lg-flex image-side">
            <h1 class="fw-bold display-4 mb-4"><i class="fas fa-lock-open"></i> Đặt lại mật khẩu</h1>
            <h3 class="fw-light mb-4">Bảo vệ tài khoản của bạn</h3>
            <p class="lead" style="opacity: 0.9;">Nhập mật khẩu mới để khôi phục quyền truy cập vào tài khoản ENGBREAK của bạn.</p>
        </div>

        <div class="col-lg-6 login-side bg-white">
            <div class="w-100" style="max-width: 420px; padding: 20px;">
                <h3 class="fw-bold text-dark mb-1">Đặt mật khẩu mới</h3>
                <p class="text-muted mb-4 pb-2">Vui lòng nhập mật khẩu mới của bạn.</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-floating mb-4">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ $email ?? old('email') }}" placeholder="Email" required autofocus>
                        <label for="email" class="text-muted"><i class="fas fa-envelope me-2"></i>Địa chỉ Email</label>
                        @error('email')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Mật khẩu mới" required
                            autocomplete="new-password">
                        <label for="password" class="text-muted"><i class="fas fa-lock me-2"></i>Mật khẩu mới</label>
                        <button class="password-toggle" type="button" id="togglePassword" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control" id="password-confirm" name="password_confirmation"
                            placeholder="Xác nhận mật khẩu mới" required autocomplete="new-password">
                        <label for="password-confirm" class="text-muted"><i class="fas fa-check-double me-2"></i>Xác nhận mật khẩu</label>
                        <button class="password-toggle" type="button" id="togglePasswordConfirm" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-reset text-white shadow-sm mb-4">
                        ĐẶT LẠI MẬT KHẨU <i class="fas fa-check ms-2"></i>
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                            <i class="fas fa-chevron-left me-1"></i> Quay lại đăng nhập
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Bật/tắt hiển thị mật khẩu
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        document.getElementById('togglePasswordConfirm')?.addEventListener('click', function() {
            const input = document.getElementById('password-confirm');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Toast notifications
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

        @if ($errors->any())
            showToast("{{ $errors->first() }}", 'error', '❌ Lỗi');
        @endif

        @if (session('status'))
            showToast("{{ session('status') }}", 'success', '✅ Thành công');
        @endif

        @if (session('error'))
            showToast("{{ session('error') }}", 'error', '❌ Lỗi');
        @endif
    </script>

</body>

</html>
