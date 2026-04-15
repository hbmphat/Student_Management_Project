@push('modals')
    <div class="modal fade" id="teacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Thêm Giảng Viên</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="teacherForm">
                    <div class="modal-body">
                        <ul id="errorList" class="alert alert-danger d-none"></ul>

                        <input type="hidden" id="teacher_id" name="teacher_id">

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Tên Giảng viên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="Nhập họ và tên">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Mã GV <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="teacher_code" name="teacher_code" required
                                    placeholder="VD: H.Phát">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giới tính <span class="text-danger">*</span></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="male">Nam</option>
                                    <option value="female">Nữ</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ngày sinh <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" required
                                    placeholder="Nhập số điện thoại">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Nhập email">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="available">Đang trống</option>
                                    <option value="teaching">Đang có lớp</option>
                                    <option value="inactive">Ngừng dạy / Tạm nghỉ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Bằng cấp / Chuyên môn</label>
                                <textarea class="form-control" id="qualifications" name="qualifications" rows="2"
                                    placeholder="VD: IELTS 8.0, TESOL..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Lưu dữ liệu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush
