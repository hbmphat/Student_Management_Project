@push('modals')
<div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Thêm Khóa Học</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="courseForm">
                <div class="modal-body">
                    <ul id="errorList" class="alert alert-danger d-none"></ul>
                    
                    <input type="hidden" id="course_id" name="course_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên khóa học<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="VD: IELTS 1">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Thời lượng (Tháng)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="duration_months" name="duration_months" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Giá / Tuần (VNĐ)<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="weekly_price" name="weekly_price" required min="0" step="1000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả khóa học</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Nhập mô tả khóa học (tùy chọn)"></textarea>
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