<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành viên | ENGBREAK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .login-side {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        .image-side {
            height: 100vh;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.9), rgba(10, 88, 202, 0.8)), url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070&auto=format&fit=crop') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            padding: 4rem;
            position: sticky;
            top: 0;
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

        .btn-register {
            padding: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            border-radius: 8px;
            background-color: #0d6efd;
            border: none;
        }

        .btn-register:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>

<body>

    <div class="row g-0">
        <div class="col-lg-6 d-none d-lg-flex image-side">
            <h1 class="fw-bold display-4 mb-4"><i class="fas fa-user-plus"></i> Gia nhập ENGBREAK</h1>
            <h3 class="fw-light mb-4">Bắt đầu hành trình quản lý chuyên nghiệp</h3>
            {{-- <p class="lead" style="opacity: 0.9;">Tạo tài khoản để trải nghiệm các tính năng điểm danh thông minh và quản lý học phí tự động.</p> --}}
        </div>

        <div class="col-lg-6 login-side bg-white">
            <div class="w-100" style="max-width: 500px; padding: 20px;">
                <h3 class="fw-bold text-dark mb-1">Tạo tài khoản mới</h3>
                <p class="text-muted mb-4">Vui lòng điền đầy đủ thông tin bên dưới.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}" placeholder="Họ và tên" required autofocus>
                        <label for="name"><i class="fas fa-id-card me-2"></i>Họ và tên</label>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}"
                                    placeholder="Tên đăng nhập" required>
                                <label for="username"><i class="fas fa-user me-2"></i>Tên đăng nhập</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text"
                                    class="form-control @error('registration_code') is-invalid @enderror"
                                    id="registration_code" name="registration_code" placeholder="Mã đăng ký" required>
                                <label for="registration_code"><i class="fas fa-key me-2"></i>Mã đăng ký</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" placeholder="Email" required>
                        <label for="email"><i class="fas fa-envelope me-2"></i>Địa chỉ Email</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Mật khẩu" required>
                        <label for="password"><i class="fas fa-lock me-2"></i>Mật khẩu</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password-confirm" name="password_confirmation"
                            placeholder="Xác nhận mật khẩu" required>
                        <label for="password-confirm"><i class="fas fa-check-double me-2"></i>Xác nhận mật khẩu</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-register text-white shadow-sm mb-4">
                        ĐĂNG KÝ NGAY <i class="fas fa-arrow-right ms-2"></i>
                    </button>

                    <div class="text-center text-muted small">
                        Đã có tài khoản? <a href="{{ route('login') }}"
                            class="text-primary fw-bold text-decoration-none">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Định nghĩa lại showToast đồng bộ với master
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

        // 1. Bắt lỗi Validation (ví dụ: sai mật khẩu, thiếu trường dữ liệu)
        @if ($errors->any())
            let errorMsg = `{{ $errors->first() }}`;
            showToast(errorMsg || 'Thông tin đăng ký không chính xác hoặc thiếu dữ liệu!', 'error', 'Đăng ký thất bại');
        @endif

        // 2. Bắt thông báo thành công từ session
        @if (session('success'))
            showToast("{{ session('success') }}", 'success', 'Đăng ký thành công');
        @endif

        // 3. Bắt thông báo lỗi từ session
        @if (session('error'))
            showToast("{{ session('error') }}", 'error', 'Lỗi');
        @endif
    </script>
</body>

</html>
