<!-- resources/views/tuitions/_modal_extend.blade.php -->
<div class="modal fade" id="extendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i> Gia hạn Học phí</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="extendForm" action="{{ url('/tuitions/extend') }}" method="POST">
                @csrf
                <input type="hidden" id="extend_tuition_id" name="tuition_id">

                <div class="modal-body">
                    <p class="mb-3">Học viên: <strong id="extend_student_name"></strong></p>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mã biên lai <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="extend_receipt_code" name="receipt_code" required placeholder="Nhập mã biên lai đã in">
                        <div class="form-text">Biên lai phải khớp với đúng học viên và lớp học hiện tại.</div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btnConfirmExtend">Xác nhận Gia hạn</button>
                </div>
            </form>
        </div>
    </div>
</div>