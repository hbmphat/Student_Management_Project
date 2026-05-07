<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ENGBREAK | Quản lý')</title>

    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = storedTheme || (prefersDark ? 'dark' : 'light');

            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.classList.toggle('dark-mode', theme === 'dark');
            document.documentElement.style.colorScheme = theme;
        })();
    </script>

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

    @include('layouts.admin._modalBackup')
    @include('layouts.admin._modalRegistrationCode')
    @include('systems.change_password')
    @include('layouts.admin._modalLogActivity')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('layouts.admin._script_backup')
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
                const backupButton = document.getElementById('btn-backup');

                if (settingsButton) {
                    settingsButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        window.toggleSettings();
                    });
                }

                if (backupButton) {
                    backupButton.addEventListener('click', function(event) {
                        event.preventDefault();

                        const passwordInput = document.getElementById('backup_confirm_password');
                        const passwordError = document.getElementById('backup_password_error');

                        if (passwordInput) {
                            passwordInput.value = '';
                            passwordInput.classList.remove('is-invalid');
                        }

                        if (passwordError) {
                            passwordError.textContent = '';
                        }

                        if (typeof showBootstrapModal === 'function') {
                            showBootstrapModal('#backupModal');
                        }
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

            document.addEventListener('DOMContentLoaded', function() {
                const themeBtn = document.getElementById('btn-toggle-theme');
                const icon = document.getElementById('theme-icon');
                const text = document.getElementById('theme-text');

                function syncThemeToggle(theme) {
                    const isDark = theme === 'dark';

                    document.documentElement.classList.toggle('dark-mode', isDark);
                    document.documentElement.setAttribute('data-bs-theme', theme);
                    document.documentElement.style.colorScheme = theme;

                    if (icon) {
                        icon.textContent = isDark ? 'light_mode' : 'dark_mode';
                    }

                    if (text) {
                        text.textContent = isDark ? 'Giao diện Sáng' : 'Giao diện Tối';
                    }

                    if (themeBtn) {
                        themeBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                        themeBtn.setAttribute('aria-label', isDark ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối');
                    }
                }

                function getPreferredTheme() {
                    const storedTheme = localStorage.getItem('theme');

                    if (storedTheme === 'dark' || storedTheme === 'light') {
                        return storedTheme;
                    }

                    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }

                syncThemeToggle(getPreferredTheme());

                if (themeBtn) {
                    themeBtn.addEventListener('click', (e) => {
                        e.preventDefault();

                        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';

                        localStorage.setItem('theme', nextTheme);
                        syncThemeToggle(nextTheme);
                    });
                }
            });
        </script>
    @endpush

    @stack('scripts')
</body>

</html>
