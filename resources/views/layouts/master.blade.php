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
    @include('layouts.sidebar')
    {{-- @include('layouts.header') --}}

    <div class="main-content" style="margin-left: 250px; margin-top: 60px; padding: 30px;">
        @yield('content')
    </div>

    @stack('modals')

    @include('layouts.admin._modalRegistrationCode')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @push('scripts')
    <script>
        function toggleSettings() {
            $('#submenu-settings').slideToggle(200);
            let arrow = $('#settings-arrow');
            if (arrow.text() === 'expand_more') {
                arrow.text('expand_less');
            } else {
                arrow.text('expand_more');
            }
        }
    </script>
    @endpush

    @stack('scripts')
</body>

</html>
