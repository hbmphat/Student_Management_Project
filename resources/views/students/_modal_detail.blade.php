<div class="modal fade" id="viewStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Hồ sơ Học viên: <span id="header-student-name" class="text-warning">...</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-4 bg-light border-end p-4 text-center">
                        <img id="detail-avatar" src="" class="rounded-circle mb-3 shadow-sm"
                            style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #fff;">
                        <h5 class="fw-bold text-primary mb-1" id="detail-name">...</h5>
                        <p class="text-muted mb-3"><i class="fas fa-id-badge"></i> <span id="detail-uuid"
                                class="fw-bold">...</span></p>

                        <div class="d-grid gap-2">
                            <a href="#" id="btn-print-card" target="_blank"
                                class="btn btn-info text-white fw-bold"><i class="fas fa-print"></i> In Thẻ Học Viên</a>

                            <button class="btn btn-outline-secondary"
                                onclick="showToast('Module Face ID đang phát triển', 'info', 'Thông báo')"><i class="fas fa-camera"></i> Đăng ký
                                Khuôn mặt</button>
                            <button class="btn btn-outline-success"><i class="fas fa-money-bill-wave"></i> Xem Học
                                Phí</button>
                        </div>
                    </div>

                    <div class="col-md-8 p-4">
                        <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">THÔNG TIN CÁ NHÂN</h6>
                        <form id="form-update-student" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Họ và Tên</label>
                                    <input type="text" name="name" id="edit-name" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Ngày sinh</label>
                                    <input type="date" name="dob" id="edit-dob" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Giới tính</label>
                                    <select name="gender" id="edit-gender" class="form-select">
                                        <option value="male">Nam</option>
                                        <option value="female">Nữ</option>
                                        <option value="other">Khác</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">SĐT Phụ huynh</label>
                                    <input type="text" name="parent_phone" id="edit-phone" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email Phụ huynh</label>
                                    <input type="email" name="parent_email" id="edit-email" class="form-control">
                                </div>
                            </div>

                            <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2 mt-4">ĐỊA CHỈ THƯỜNG TRÚ</h6>
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">Tỉnh/Thành phố</label>
                                    <select class="form-select form-select-sm" id="edit-province-id">
                                        <option value="">-- Chọn Tỉnh --</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">Quận/Huyện</label>
                                    <select class="form-select form-select-sm" id="edit-district-id">
                                        <option value="">-- Chọn Huyện --</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">Phường/Xã</label>
                                    <select class="form-select form-select-sm" name="ward_id" id="edit-ward-id">
                                        <option value="">-- Chọn Xã --</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Số nhà, Tên đường</label>
                                    <input type="text" name="street_address" id="edit-street-address"
                                        class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-4 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Cập nhật Ảnh đại diện</label>
                                    <input type="file" name="avatar" class="form-control form-control-sm"
                                        accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Trạng thái học tập</label>
                                    <select name="status" id="edit-status"
                                        class="form-select fw-bold border-danger">
                                        <option value="studying">Đang học</option>
                                        <option value="reserved">Bảo lưu</option>
                                        <option value="dropped">Đã nghỉ học</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between pt-3 border-top">
                                <button type="button" class="btn btn-outline-danger" onclick="deleteStudent()"><i
                                        class="fas fa-trash"></i> Xóa Học viên</button>
                                <button type="submit" class="btn btn-warning fw-bold"><i class="fas fa-save"></i>
                                    Cập Nhật Lưu Trữ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
