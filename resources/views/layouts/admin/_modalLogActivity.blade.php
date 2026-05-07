<div class="modal fade" id="modalLogActivity" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-history me-2"></i> Nhật ký hoạt động hệ thống</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-4">Thời gian</th>
                                <th>Người thực hiện</th>
                                <th>Hành động</th>
                                <th>Bảng</th>
                                <th>Đối tượng</th>
                                <th class="text-end pe-4">Chi tiết thay đổi</th>
                            </tr>
                        </thead>
                        <tbody id="log-activity-tbody">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLogDetailView" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title">So sánh thay đổi dữ liệu</h6>
                <button type="button" class="btn-close btn-close-white" onclick="closeLogDetail()"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Trường dữ liệu</th>
                            <th>Giá trị cũ</th>
                            <th>Giá trị mới</th>
                        </tr>
                    </thead>
                    <tbody id="log-detail-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Hàm mở Modal chính
    window.openActivityLogModal = function() {
        showBootstrapModal('#modalLogActivity');
        loadActivityLogs();
    };

    function loadActivityLogs() {
        const tbody = $('#log-activity-tbody');
        tbody.html('<tr><td colspan="5" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Đang tải nhật ký...</td></tr>');

        $.get("{{ route('systems.activity-logs') }}", function(data) {
            let html = '';
            data.forEach(log => {
                let badgeColor = { 'created': 'success', 'updated': 'warning', 'deleted': 'danger' }[log.event] || 'secondary';
                let eventText = { 'created': 'Thêm mới', 'updated': 'Cập nhật', 'deleted': 'Xóa' }[log.event] || log.event;

                html += `
                    <tr>
                        <td class="ps-4 text-muted small">${log.time}<br>(${log.time_ago})</td>
                        <td class="fw-bold">${log.causer}</td>
                        <td><span class="badge bg-${badgeColor}">${eventText}</span></td>
                        <td><span class="text-primary fw-bold">${log.table_name}</span></td>
                        <td><span class="text-primary fw-bold">${log.subject}</span></td>
                        <td class="text-end pe-4">
                            ${log.event === 'updated' ? `<button class="btn btn-sm btn-outline-info" onclick='viewLogDetail(${JSON.stringify(log.properties)})'>So sánh</button>` : `<small class="text-muted fst-italic">Không có chi tiết</small>`}
                        </td>
                    </tr>`;
            });
            tbody.html(html || '<tr><td colspan="5" class="text-center py-4">Chưa có hoạt động nào</td></tr>');
        });
    }

    // Logic "Dịch" JSON sang Bảng so sánh
    window.viewLogDetail = function(props) {
        let html = '';
        const oldData = props.old || {};
        const newData = props.attributes || {};

        // Lấy tất cả các key từ cả old và new
        const keys = [...new Set([...Object.keys(oldData), ...Object.keys(newData)])];

        keys.forEach(key => {
            if(key === 'updated_at') return; // Bỏ qua cột thời gian cập nhật
            
            let oldVal = oldData[key] ?? '<span class="text-muted small">Trống</span>';
            let newVal = newData[key] ?? '<span class="text-muted small">Trống</span>';
            
            // Chỉ hiện những dòng có sự thay đổi thực sự
            if (oldVal !== newVal) {
                html += `
                <tr>
                    <td class="fw-bold bg-light" style="width: 30%">${key}</td>
                    <td class="text-danger" style="width: 35%"><del>${oldVal}</del></td>
                    <td class="text-success fw-bold" style="width: 35%">${newVal}</td>
                </tr>`;
            }
        });

        $('#log-detail-table-body').html(html || '<tr><td colspan="3" class="text-center text-muted">Không có thay đổi dữ liệu cụ thể</td></tr>');
        showBootstrapModal('#modalLogDetailView');
    };

    window.closeLogDetail = function() {
        hideBootstrapModal('#modalLogDetailView');
    };
</script>
@endpush