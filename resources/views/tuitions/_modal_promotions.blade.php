<!-- 1. MODAL DANH SÁCH KHUYẾN MÃI -->
<div class="modal fade" id="listPromotionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-tags me-2"></i> Quản lý Khuyến mãi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom d-flex justify-content-end">
                    <!-- Nút mở Form Thêm mới -->
                    <button class="btn btn-primary fw-bold" onclick="openPromoFormModal()">
                        <i class="fas fa-plus-circle"></i> Thêm Khuyến mãi
                    </button>
                </div>
                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-4">Tên chương trình</th>
                                <th>Mức giảm</th>
                                <th class="text-center">Bật/Tắt</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="promotions-tbody">
                            <!-- Dữ liệu AJAX đổ vào đây -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. MODAL FORM THÊM/SỬA KHUYẾN MÃI -->
<div class="modal fade" id="formPromotionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="promoModalTitle"><i class="fas fa-tag"></i> Thêm Khuyến mãi</h5>
                <!-- Khi tắt Form, quay lại Modal Danh sách -->
                <button type="button" class="btn-close btn-close-white" onclick="backToListModal()"></button>
            </div>
            <form id="promotionForm">
                <div class="modal-body">
                    <input type="hidden" id="promo_id" name="promo_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên chương trình <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="promo_name" name="name" required placeholder="VD: Khuyến mãi hè">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mức giảm giá (%) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="promo_discount" name="discount_percent" required min="1" max="100" placeholder="VD: 10">
                            <span class="input-group-text bg-light">%</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea class="form-control" id="promo_description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" onclick="backToListModal()">Huỷ</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btnSavePromo">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- LUỒNG DANH SÁCH ---
    window.openListPromotionsModal = function() {
        showBootstrapModal('#listPromotionsModal');
        loadPromotions();
    }

    function loadPromotions() {
        $('#promotions-tbody').html('<tr><td colspan="4" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>');
        $.get('/promotions', function(res) {
            let html = '';
            let paymentSelectHtml = '<option value="" data-discount="0">-- Không áp dụng --</option>'; // Để update luôn ô Select ngoài Modal Thanh Toán

            res.forEach(p => {
                // Update HTML cho bảng
                let isChecked = p.is_active ? 'checked' : '';
                html += `
                    <tr>
                        <td class="ps-4 fw-bold">${p.name}<br><small class="text-muted fw-normal">${p.description || ''}</small></td>
                        <td><span class="badge bg-danger">-${p.discount_percent}%</span></td>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" role="switch" style="transform: scale(1.2);" ${isChecked} onchange="togglePromo(${p.id})">
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary" onclick="editPromo(${p.id})"><i class="fas fa-edit">Sửa</i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deletePromo(${p.id}, '${p.name}')"><i class="fas fa-trash">Xoá</i></button>
                        </td>
                    </tr>
                `;

                // Tự động build lại danh sách cho thẻ Select Thanh Toán (Chỉ lấy cái đang Active)
                if(p.is_active) {
                    paymentSelectHtml += `<option value="${p.id}" data-discount="${p.discount_percent}">${p.name} (Giảm ${p.discount_percent}%)</option>`;
                }
            });
            $('#promotions-tbody').html(html || '<tr><td colspan="4" class="text-center py-3 text-muted">Trống</td></tr>');
            
            // Cập nhật lại dropdown khuyến mãi trong Modal Thanh toán (luồng 1)
            $('#pay_promotion').html(paymentSelectHtml);
        });
    }

    // Bật/Tắt
    window.togglePromo = function(id) {
        $.post(`/promotions/${id}/toggle`, { _token: '{{ csrf_token() }}' }, function(res) {
            showToast(res.message, 'success');
            loadPromotions(); // load lại để update select box
        });
    }

    // Xóa
    window.deletePromo = function(id, name) {
        showConfirmDialog({ text: `Xóa khuyến mãi "${name}"?`, confirmButtonText: 'Xóa' }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/promotions/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                    showToast(res.message, 'success'); loadPromotions();
                    }
                });
            }
        });
    }

    // --- LUỒNG FORM THÊM/SỬA ---
    window.openPromoFormModal = function() {
        $('#promotionForm')[0].reset();
        $('#promo_id').val('');
        $('#promoModalTitle').html('<i class="fas fa-plus"></i> Thêm Khuyến mãi');
        hideBootstrapModal('#listPromotionsModal');
        showBootstrapModal('#formPromotionModal');
    }

    window.editPromo = function(id) {
        $.get(`/promotions/${id}`, function(res) {
            $('#promo_id').val(res.id);
            $('#promo_name').val(res.name);
            $('#promo_discount').val(res.discount_percent);
            $('#promo_description').val(res.description);
            $('#promoModalTitle').html('<i class="fas fa-edit"></i> Cập nhật Khuyến mãi');
            hideBootstrapModal('#listPromotionsModal');
            showBootstrapModal('#formPromotionModal');
        });
    }

    window.backToListModal = function() {
        hideBootstrapModal('#formPromotionModal');
        showBootstrapModal('#listPromotionsModal');
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('#promotionForm').submit(function(e) {
            e.preventDefault();
            let btn = $('#btnSavePromo');
            btn.prop('disabled', true);

            let id = $('#promo_id').val();
            $.ajax({
                url: id ? `/promotions/${id}` : '/promotions',
                type: id ? 'PUT' : 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(res) {
                    btn.prop('disabled', false);
                    showToast(res.message, 'success', 'Thành công');
                    backToListModal(); // Quay lại bảng
                    loadPromotions(); // Tải lại bảng
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    const message = xhr.responseJSON?.message || Object.values(xhr.responseJSON?.errors || {}).flat().join('\n') || 'Lỗi! Vui lòng kiểm tra dữ liệu';
                    showToast(message, 'error', 'Thất bại');
                }
            });
        });
    });
</script>