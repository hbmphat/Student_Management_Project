<div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Khởi tạo Lớp học mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-add-class">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Khóa học <span class="text-danger">*</span></label>
                            <select class="form-select" name="course_id" required>
                                <option value="">-- Chọn khóa học --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}
                                        ({{ $course->duration_months }} tháng)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giảng viên <span class="text-danger">*</span></label>
                            <select class="form-select" name="teacher_id" required>
                                <option value="">-- Chọn giảng viên --</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ca học <span class="text-danger">*</span></label>
                            <select class="form-select" name="shift_id" required>
                                <option value="">-- Chọn ca học --</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phòng học dự kiến</label>
                            <input type="text" class="form-control" name="room" placeholder="VD: P.201">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày khai giảng <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày kết thúc dự kiến <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">Lịch học trong tuần (Chọn ít nhất 1 ngày) <span
                                    class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border">
                                @foreach ([2 => 'Thứ 2', 3 => 'Thứ 3', 4 => 'Thứ 4', 5 => 'Thứ 5', 6 => 'Thứ 6', 7 => 'Thứ 7', 8 => 'Chủ nhật'] as $val => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="days_of_week[]"
                                            value="{{ $val }}" id="day_{{ $val }}">
                                        <label class="form-check-label fw-bold"
                                            for="day_{{ $val }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btn-save-class">Lưu & Tạo Lớp</button>
                </div>
            </form>
        </div>
    </div>
</div>
