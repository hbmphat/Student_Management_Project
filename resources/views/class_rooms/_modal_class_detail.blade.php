<div class="modal fade" id="viewClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="detail-class-name">Đang tải thông tin lớp...</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <form id="form-update-class">
                    @csrf
                    @method('PUT')
                    <div class="row g-0">
                        <div class="col-md-5 bg-light border-end p-4">
                            <h6 class="fw-bold text-primary mb-3">CHỈNH SỬA THÔNG TIN LỚP</h6>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Khóa học</label>
                                <select class="form-select form-select-sm" name="course_id" id="edit-course-id">
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Giảng viên</label>
                                <select class="form-select form-select-sm" name="teacher_id" id="edit-teacher-id">
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Ca học</label>
                                    <select class="form-select form-select-sm" name="shift_id" id="edit-shift-id">
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Phòng học</label>
                                    <input type="text" name="room" id="edit-room"
                                        class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Ngày khai giảng</label>
                                    <input type="date" name="start_date" id="edit-start-date"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Ngày kết thúc</label>
                                    <input type="date" name="end_date" id="edit-end-date"
                                        class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-danger">Lịch học trong tuần</label>
                                <div class="d-flex flex-wrap gap-2 p-2 bg-white rounded border">
                                    @foreach ([2 => 'T2', 3 => 'T3', 4 => 'T4', 5 => 'T5', 6 => 'T6', 7 => 'T7', 8 => 'CN'] as $val => $label)
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input edit-day-checkbox" type="checkbox"
                                                name="days_of_week[]" value="{{ $val }}"
                                                id="edit_day_{{ $val }}">
                                            <label class="form-check-label small"
                                                for="edit_day_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted italic">* Thay đổi lịch sẽ tự động tính lại các buổi học chưa
                                    diễn ra.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Trạng thái lớp</label>
                                <select name="status" id="edit-status"
                                    class="form-select form-select-sm border-primary text-primary fw-bold">
                                    <option value="pending">Sắp mở</option>
                                    <option value="active">Đang học</option>
                                    <option value="completed">Kết thúc</option>
                                    <option value="canceled">Đã hủy</option>
                                </select>
                            </div>

                            <div class="mt-4 d-grid gap-2">
                                <button type="submit" class="btn btn-warning fw-bold"><i class="fas fa-save"></i> CẬP
                                    NHẬT TOÀN BỘ</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteClass()">Xóa
                                    Lớp</button>
                            </div>
                        </div>

                        <div class="col-md-7 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-success m-0">DANH SÁCH HỌC VIÊN (<span
                                        id="detail-student-count">0</span>)</h6>
                                <button type="button" class="btn btn-sm btn-secondary" disabled><i
                                        class="fas fa-user-plus"></i> Thêm Học viên</button>
                            </div>
                            <div class="table-responsive" style="max-height: 500px;">
                                <table class="table table-bordered align-middle shadow-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên Học viên</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="student-list-body">
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Chưa có dữ liệu học
                                                viên...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
