<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-lock-open me-2"></i> Đổi mật khẩu tài khoản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-4">Để đảm bảo bảo mật, vui lòng xác thực mã nhân sự và mật khẩu cũ trước khi đặt mật khẩu mới.</p>

                    <div class="form-floating mb-3">
                        <input type="text" name="registration_code" class="form-control" id="cp_reg_code" placeholder="Mã đăng ký" required>
                        <label for="cp_reg_code"><i class="fas fa-key me-2"></i>Mã nhân viên (Mã đăng ký)</label>
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="form-floating mb-3">
                        <input type="password" name="old_password" class="form-control" id="cp_old_pass" placeholder="Mật khẩu cũ" required>
                        <label for="cp_old_pass"><i class="fas fa-shield-alt me-2"></i>Mật khẩu hiện tại</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" id="cp_new_pass" placeholder="Mật khẩu mới" required>
                        <label for="cp_new_pass"><i class="fas fa-fingerprint me-2"></i>Mật khẩu mới (ít nhất 8 ký tự)</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="password_confirmation" class="form-control" id="cp_confirm_pass" placeholder="Xác nhận" required>
                        <label for="cp_confirm_pass"><i class="fas fa-check-double me-2"></i>Xác nhận mật khẩu mới</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" id="btnSubmitChangePassword" class="btn btn-primary fw-bold px-4">
                        CẬP NHẬT NGAY <i class="fas fa-save ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Hàm mở Modal được gọi từ Sidebar
    window.openChangePasswordModal = function() {
        $('#changePasswordForm')[0].reset();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('changePasswordModal')).show();
    };

    // Xử lý gửi AJAX đổi mật khẩu
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();
        let btn = $('#btnSubmitChangePassword');
        let originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xác thực...');

        $.ajax({
            url: "{{ route('profile.change-password') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
                btn.prop('disabled', false).html(originalText);
                
                // Gọi hàm showToast đã có trong master.blade.php
                if (typeof showToast === "function") {
                    showToast(res.message, 'success', 'Thành công');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                let errorMsg = 'Có lỗi xảy ra, vui lòng thử lại.';
                
                if (xhr.status === 422) {
                    // Lấy thông báo lỗi validation đầu tiên từ Laravel
                    errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                }
                
                if (typeof showToast === "function") {
                    showToast(errorMsg, 'error', 'Thất bại');
                }
            }
        });
    });
</script>
@endpush