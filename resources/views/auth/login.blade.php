<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | ENGBREAK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .login-side {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-side {
            height: 100vh;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.9), rgba(10, 88, 202, 0.8)), url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=2070&auto=format&fit=crop') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            padding: 4rem;
        }

        /* Tùy chỉnh cho Floating Label mượt mà hơn */
        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            color: #0d6efd;
            font-weight: bold;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
        }

        .btn-login {
            padding: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            border-radius: 8px;
        }

        /* Nút ẩn hiện mật khẩu */
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
            <h1 class="fw-bold display-4 mb-4"><i class="fas fa-graduation-cap"></i> ENGBREAK</h1>
            <h3 class="fw-light mb-4">Hệ sinh thái Quản lý Đào tạo Thông minh</h3>
            {{-- <p class="lead" style="opacity: 0.9;">
            Tối ưu hóa quy trình vận hành, điểm danh AI siêu tốc và quản lý học phí minh bạch. Mọi thứ bạn cần đều nằm trong tầm tay.
        </p> --}}
        </div>

        <div class="col-lg-6 login-side bg-white">
            <div class="w-100" style="max-width: 420px; padding: 20px;">
                <div class="text-center mb-5 d-lg-none">
                    <h2 class="fw-bold text-primary"><i class="fas fa-graduation-cap"></i> ENGBREAK</h2>
                </div>

                <h3 class="fw-bold text-dark mb-1">Chào mừng trở lại!</h3>
                <p class="text-muted mb-4 pb-2">Vui lòng đăng nhập để truy cập hệ thống.</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-floating mb-4">
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                            id="username" name="username" value="{{ old('username') }}" placeholder="Tên đăng nhập"
                            required autofocus autocomplete="username">
                        <label for="username" class="text-muted"><i class="fas fa-user me-2"></i>Tên đăng nhập</label>
                        @error('username')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Mật khẩu" required
                            autocomplete="current-password">
                        <label for="password" class="text-muted"><i class="fas fa-lock me-2"></i>Mật khẩu</label>
                        <button class="password-toggle" type="button" id="togglePassword" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-login mb-4 shadow-sm">
                        ĐĂNG NHẬP <i class="fas fa-sign-in-alt ms-2"></i>
                    </button>

                    <div class="text-center mt-3">
                        @if (Route::has('password.request'))
                            <a class="text-muted text-decoration-none small d-block mb-2 hover-primary"
                                href="{{ route('password.request') }}">
                                <i class="fas fa-key me-1"></i> Quên mật khẩu?
                            </a>
                        @endif

                        @if (Route::has('register'))
                            <div class="text-muted small mt-3">
                                Chưa có tài khoản? <a href="{{ route('register') }}"
                                    class="text-primary fw-bold text-decoration-none">Đăng ký ngay</a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Logic bật/tắt hiển thị mật khẩu
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // 2. Tái sử dụng hàm showToast giống hệt trong master.blade.php
        window.showToast = function(message, icon = 'success', title = 'Thông báo', options = {}) {
            return Swal.fire({
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
                },
                ...options,
            });
        };

        // 3. Bắt lỗi sai tài khoản / mật khẩu từ Laravel trả về
        @if ($errors->any())
            showToast('{{ $errors->first() }}', 'error', 'Đăng nhập thất bại');
        @endif

        // 4. Bắt lỗi tài khoản bị khóa (từ LoginController)
        @if (session('error'))
            showToast("{{ session('error') }}", 'error', 'Cảnh báo');
        @endif

        // 5. Bắt thông báo thành công (nếu có session success)
        @if (session('success'))
            showToast("{{ session('success') }}", 'success', 'Chào mừng');
        @endif
    </script>

</body>

</html>
