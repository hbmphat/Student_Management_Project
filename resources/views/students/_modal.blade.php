<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Tiếp nhận Học viên mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-add-student" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="dob" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Giới tính <span class="text-danger">*</span></label>
                            <select class="form-select" name="gender">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">SĐT Phụ huynh <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="parent_phone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Email Phụ huynh</label>
                            <input type="email" class="form-control" name="parent_email">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Địa chỉ</label>
                        <input type="text" class="form-control" name="address">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Ảnh đại diện (Avatar)</label>
                        <input type="file" class="form-control" name="avatar" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btn-save-student">Lưu Thông Tin</button>
                </div>
            </form>
        </div>
    </div>
</div>