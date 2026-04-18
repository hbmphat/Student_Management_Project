<aside class="sidebar">
    <div class="brand d-flex align-items-center p-3 mt-2">
        <span class="material-symbols-outlined text-white fs-1 me-2">school</span>
        <h2 class="m-0 fw-bold text-white">EngBreak</h2>
    </div>

    <div class="user-info-box mx-3 mb-4 p-2 rounded d-flex align-items-center" style="background: rgba(255,255,255,0.1);">
        <div class="user-avatar-circle bg-white text-dark fw-bold rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
            {{ strtoupper(mb_substr(Auth::user()->name, 0, 1, 'UTF-8')) }}
        </div>
        <div class="user-details overflow-hidden">
            <div class="fw-bold text-white text-truncate" title="{{ Auth::user()->name }}">
                {{ Auth::user()->name }}
            </div>
            <div class="text-white-50" style="font-size: 0.85rem;">
                {{ Auth::user()->role === 'admin' ? 'Quản trị viên' : 'Nhân viên' }}
            </div>
        </div>
    </div>

    <nav class="nav-menu d-flex flex-column">
        <a class="nav-item" href="#">
            <span class="material-symbols-outlined">dashboard</span>
            <p class="m-0">Trang Chủ</p>
        </a>
        
        <a class="nav-item" href="#">
            <span class="material-symbols-outlined">group</span>
            <p class="m-0">Quản lý Học viên</p>
        </a>

        <a class="nav-item {{ request()->routeIs('class-rooms.*') ? 'active' : '' }}" href="{{ route('class-rooms.index') }}">
            <span class="material-symbols-outlined">desk</span>
            <p class="m-0">Quản lý Lớp học</p>
        </a>

        <a class="nav-item {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">
            <span class="material-symbols-outlined">import_contacts</span>
            <p class="m-0">Quản lý Khóa học</p>
        </a>

        <a class="nav-item" href="#">
            <span class="material-symbols-outlined">payment</span>
            <p class="m-0">Quản lý Thanh toán</p>
        </a>

        <a class="nav-item {{ request()->routeIs('teachers.*') ? 'active' : '' }}" href="{{ route('teachers.index') }}">
            <span class="material-symbols-outlined">school</span>
            <p class="m-0">Quản lý Giảng viên</p>
        </a>

        <a class="nav-item" href="#">
            <span class="material-symbols-outlined">camera_video</span>
            <p class="m-0">Điểm danh Học viên</p>
        </a>

        <div class="nav-group mt-3">
            <div class="nav-item" id="btn-settings" style="cursor: pointer;" onclick="toggleSettings()">
                <span class="material-symbols-outlined">settings</span>
                <p class="m-0 flex-grow-1">Cài đặt chung</p>
                <span class="material-symbols-outlined" id="settings-arrow">expand_more</span>
            </div>
            
            <div class="submenu flex-column bg-dark bg-opacity-25" id="submenu-settings" style="display: none;">
                <a href="#" class="sub-item" onclick="#">
                    <span class="material-symbols-outlined fs-5">dark_mode</span>
                    <span>Giao diện Tối</span>
                </a>
                
                @if(Auth::user()->role === 'admin')
                    <a href="#" class="sub-item" onclick="#">
                        <span class="material-symbols-outlined fs-5">cloud_download</span>
                        <span>Backup dữ liệu</span>
                    </a>
                    <a href="#" class="sub-item" onclick="openAdminCodeModal()">
                        <span class="material-symbols-outlined fs-5">vpn_key</span>
                        <span>Tạo Key NV</span>
                    </a>
                    <a href="#" class="sub-item">
                        <span class="material-symbols-outlined fs-5">history</span>
                        <span>Lịch sử hoạt động</span>
                    </a>
                @endif

                <a href="#" class="sub-item" onclick="#">
                    <span class="material-symbols-outlined fs-5">lock_reset</span>
                    <span>Đổi mật khẩu</span>
                </a>
                
                <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form" class="d-none">@csrf</form>
                <a href="#" class="sub-item text-danger" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                    <span class="material-symbols-outlined fs-5">logout</span>
                    <span class="fw-bold">Đăng xuất</span>
                </a>
            </div>
        </div>
    </nav>
</aside>