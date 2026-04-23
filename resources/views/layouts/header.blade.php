<header class="header d-flex justify-content-between align-items-center px-3">
    
    <!-- LEFT -->
    <div class="d-flex align-items-center">
        <button id="btn-toggle-sidebar" class="btn btn-light d-lg-none me-3"
            style="border: none; background: transparent;">
            <span class="material-symbols-outlined fs-2 text-dark">menu</span>
        </button>
    </div>

    <!-- LOGO (mobile) -->
    <div class="d-lg-none d-flex align-items-center">
        <span class="material-symbols-outlined text-black fs-1 me-2">school</span>
        <h2 class="m-0 fw-bold text-black">EngBreak</h2>
    </div>

    <!-- RIGHT: USER -->
    <div class="d-flex align-items-center gap-2 d-lg-none">
        <div class="rounded-circle bg-dark text-white d-flex justify-content-center align-items-center"
            style="width: 35px; height: 35px;">
            {{ strtoupper(mb_substr(Auth::user()->name, 0, 1, 'UTF-8')) }}
        </div>
    </div>

</header>