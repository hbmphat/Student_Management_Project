@if(Auth::user()->role === 'admin')
<div class="modal fade" id="adminCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-shield-alt"></i> Xác thực quyền Quản trị</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adminCodeForm">
                <div class="modal-body">
                    <p class="text-muted">Vui lòng nhập mật khẩu của bạn để tạo mã đăng ký nhân sự mới.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu của bạn</label>
                        <input type="password" class="form-control" id="admin_confirm_password" required placeholder="Nhập mật khẩu...">
                        <div class="invalid-feedback fw-bold" id="admin_password_error"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark" id="btnGenerateCode">Xác nhận & Tạo mã</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Hàm mở Modal (Gọi từ Sidebar)
    function openAdminCodeModal() {
        $('#admin_confirm_password').val('');
        $('#admin_confirm_password').removeClass('is-invalid');
        $('#adminCodeModal').modal('show');
    }

    // Xử lý gửi mật khẩu qua AJAX
    $('#adminCodeForm').submit(function(e) {
        e.preventDefault();
        
        let btn = $('#btnGenerateCode');
        btn.prop('disabled', true).text('Đang xử lý...');

        $.ajax({
            url: '/registration-codes',
            type: 'POST',
            data: {
                password: $('#admin_confirm_password').val()
            },
            success: function(response) {
                $('#adminCodeModal').modal('hide');
                btn.prop('disabled', false).text('Xác nhận & Tạo mã');
                
                // Hiển thị mã thành công bà Tân Vlog
                Swal.fire({
                    icon: 'success',
                    title: 'Cấp mã thành công!',
                    html: `Gửi mã này cho nhân viên để đăng ký:<br><br><b class="fs-1 text-danger" style="letter-spacing: 2px;">${response.code}</b>`,
                    confirmButtonColor: '#0b3d91',
                    confirmButtonText: 'Đã copy'
                }).then(() => {
                    // Nếu đang đứng ở trang Lịch sử thì reload lại bảng
                    if(window.location.pathname === '/registration-codes') {
                        location.reload();
                    }
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Xác nhận & Tạo mã');
                if(xhr.status === 422) {
                    // Mật khẩu sai
                    $('#admin_confirm_password').addClass('is-invalid');
                    $('#admin_password_error').text(xhr.responseJSON.errors.password[0]);
                } else {
                    Swal.fire('Lỗi!', 'Hệ thống đang bận, vui lòng thử lại.', 'error');
                }
            }
        });
    });
</script>
@endif