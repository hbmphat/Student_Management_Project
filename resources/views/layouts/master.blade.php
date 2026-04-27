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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

        window.showBootstrapModal = function(selector) {
            const element = document.querySelector(selector);

            if (!element) {
                return;
            }

            bootstrap.Modal.getOrCreateInstance(element).show();
        };

        window.hideBootstrapModal = function(selector) {
            const element = document.querySelector(selector);

            if (!element) {
                return;
            }

            bootstrap.Modal.getOrCreateInstance(element).hide();
        };

        window.toggleSettings = function() {
            const submenu = document.getElementById('submenu-settings');
            const arrow = document.getElementById('settings-arrow');

            if (!submenu || !arrow) {
                return;
            }

            const isHidden = submenu.style.display === 'none' || submenu.style.display === '';
            submenu.style.display = isHidden ? 'flex' : 'none';
            arrow.textContent = isHidden ? 'expand_less' : 'expand_more';
        };

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const toggleSidebarButton = document.getElementById('btn-toggle-sidebar');
            const settingsButton = document.getElementById('btn-settings');

            if (settingsButton) {
                settingsButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    window.toggleSettings();
                });
            }

            if (toggleSidebarButton && sidebar && sidebarOverlay) {
                toggleSidebarButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('active');
                });

                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('active');
                });
            }
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
