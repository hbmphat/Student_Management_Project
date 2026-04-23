<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ENGBREAK | Quản lý')</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('layouts.sidebar')
    @include('layouts.header')

    <div class="main-content" style="margin-left: 250px; margin-top: 60px; padding: 30px;">
        @yield('content')
    </div>

    @stack('modals')

    @include('layouts.admin._modalRegistrationCode')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @push('scripts')
    <script>
        window.showToast = function(message, icon = 'success', title = 'Thông báo', options = {}) {
            return Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: title,
                text: message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                ...options,
            });
        };

        window.showConfirmDialog = function(options = {}) {
            return Swal.fire({
                title: 'Xác nhận',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                reverseButtons: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                ...options,
            });
        };

        function toggleSettings() {
            $('#submenu-settings').slideToggle(200);
            let arrow = $('#settings-arrow');
            if (arrow.text() === 'expand_more') {
                arrow.text('expand_less');
            } else {
                arrow.text('expand_more');
            }
        }
        $(document).ready(function() {
            $('#btn-toggle-sidebar').click(function() {
                $('.sidebar').toggleClass('show');
                $('#sidebarOverlay').toggleClass('active');
            });

            $('#sidebarOverlay').click(function() {
                $('.sidebar').removeClass('show');
                $(this).removeClass('active');
            });
        });

        @if (session('success'))
            showToast(@json(session('success')), 'success', 'Thành công');
        @endif

        @if (session('error'))
            showToast(@json(session('error')), 'error', 'Thất bại');
        @endif
    </script>
    @endpush

    @stack('scripts')
</body>

</html>
