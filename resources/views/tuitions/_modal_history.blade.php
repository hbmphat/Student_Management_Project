<!-- resources/views/tuitions/_modal_history.blade.php -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-history me-2"></i> Lịch sử Học phí</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="text-muted small">Học viên</div>
                            <div class="fw-bold" id="history_student_name">-</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="text-muted small">Lớp học</div>
                            <div class="fw-bold" id="history_class_name">-</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="text-muted small">Hạn học phí hiện tại</div>
                            <div class="fw-bold" id="history_current_period">-</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0"><i class="fas fa-receipt text-primary me-2"></i>Lịch sử thanh toán</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th>Mã biên lai</th>
                                    <th>Tuần đóng</th>
                                    <th>Khuyến mãi</th>
                                    <th class="text-end">Thực thu</th>
                                    <th class="text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody id="history_payments_tbody">
                                <tr><td colspan="6" class="text-center py-3 text-muted">Chọn một học viên để xem lịch sử.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0"><i class="fas fa-calendar-alt text-success me-2"></i>Lịch sử thay đổi hạn học phí</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th>Loại</th>
                                    <th>Mã biên lai</th>
                                    <th>Hạn cũ</th>
                                    <th>Hạn mới</th>
                                </tr>
                            </thead>
                            <tbody id="history_period_tbody">
                                <tr><td colspan="5" class="text-center py-3 text-muted">Chọn một học viên để xem lịch sử.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>