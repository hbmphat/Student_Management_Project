@if (Auth::user()->role === 'admin')
    <div class="modal fade" id="adminCodeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-shield-alt"></i> Xác thực quyền Quản trị</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="adminCodeForm">
                    <div class="modal-body">
                        <p class="text-muted">Vui lòng nhập mật khẩu của bạn để tạo mã đăng ký nhân sự mới.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu của bạn</label>
                            <input type="password" class="form-control" id="admin_confirm_password" required
                                placeholder="Nhập mật khẩu...">
                            <div class="invalid-feedback fw-bold" id="admin_password_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-primary fw-bold"
                            onclick="openAdminListCodesModal()">
                            <i class="fas fa-list-ul"></i> Quản lý mã
                        </button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-warning fw-bold text-dark" id="btnGenerateCode">Xác
                                nhận & Tạo mã</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adminListCodesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-key"></i> Quản lý Mã đăng ký nhân sự</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4">Mã / Nhân viên</th>
                                    <th>Tên đăng nhập</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="registration-codes-list">
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted"><i
                                            class="fas fa-spinner fa-spin me-2"></i>Đang tải dữ liệu...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ------------------------------------
            // LOGIC TẠO MÃ (Giữ nguyên của bạn)
            // ------------------------------------
            function openAdminCodeModal() {
                $('#admin_confirm_password').val('').removeClass('is-invalid');
                hideBootstrapModal('#adminListCodesModal'); // Ẩn modal danh sách nếu đang mở
                showBootstrapModal('#adminCodeModal');
            }

            $('#adminCodeForm').submit(function(e) {
                e.preventDefault();
                let btn = $('#btnGenerateCode');
                btn.prop('disabled', true).text('Đang xử lý...');

                $.ajax({
                    url: '/registration-codes',
                    type: 'POST',
                    data: {
                        password: $('#admin_confirm_password').val()
                    },
                    success: function(response) {
                        hideBootstrapModal('#adminCodeModal');
                        btn.prop('disabled', false).text('Xác nhận & Tạo mã');

                        showToast('', 'success', 'Cấp mã thành công!', {
                            html: `Gửi mã này cho nhân viên để đăng ký:<br><br><b class="fs-1 text-danger" style="letter-spacing: 2px;">${response.code}</b>`,
                            timer: 10000,
                        }).then(() => {
                            if (window.location.pathname === '/registration-codes') location
                                .reload();
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Xác nhận & Tạo mã');
                        if (xhr.status === 422) {
                            $('#admin_confirm_password').addClass('is-invalid');
                            $('#admin_password_error').text(xhr.responseJSON.errors.password[0]);
                        } else {
                            showToast('Hệ thống đang bận, vui lòng thử lại.', 'error', 'Thất bại');
                        }
                    }
                });
            });

            // ------------------------------------
            // LOGIC QUẢN LÝ DANH SÁCH MÃ
            // ------------------------------------
            function openAdminListCodesModal() {
                hideBootstrapModal('#adminCodeModal'); // Ẩn modal tạo mã
                showBootstrapModal('#adminListCodesModal');
                loadRegistrationCodes();
            }

            function loadRegistrationCodes() {
                const tbody = $('#registration-codes-list');
                tbody.html(
                    '<tr><td colspan="4" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Đang tải...</td></tr>'
                );

                $.get('/registration-codes', function(codes) {
                    let html = '';
                    codes.forEach(c => {
                        let staffName = c.user ? `<div class="fw-bold text-dark">${c.user.name}</div>` :
                            '<i class="text-muted small">Chưa có</i>';
                        let username = c.user ?
                            `<span class="badge bg-light text-primary border">${c.user.username}</span>` :
                            '---';

                        // Logic hiển thị Badge Trạng thái
                        let statusBadge = '';
                        if (c.is_blocked) {
                            statusBadge =
                                `<span class="badge bg-danger"><i class="fas fa-user-slash"></i> Đã khóa</span>`;
                        } else {
                            statusBadge = c.is_used ?
                                `<span class="badge bg-success"><i class="fas fa-check"></i> Đang dùng</span>` :
                                `<span class="badge bg-info text-white"><i class="fas fa-clock"></i> Sẵn sàng</span>`;
                        }

                        // Nút Khóa/Mở khóa
                        let blockBtn = `
                <button class="btn btn-sm ${c.is_blocked ? 'btn-outline-success' : 'btn-outline-danger'}" 
                        onclick="toggleBlockRegCode(${c.id})" 
                        title="${c.is_blocked ? 'Mở khóa' : 'Khóa mã'}">
                    <i class="fas ${c.is_blocked ? 'fa-unlock' : 'fa-user-lock'}"></i>${c.is_blocked ? 'Mở khóa' : 'Khóa'}
                </button>
            `;

                        // Nút Xóa (Chỉ hiện nếu chưa dùng và chưa khóa)
                        let deleteBtn = !c.is_used ? `
                <button class="btn btn-sm btn-outline-danger text-danger" onclick="deleteRegCode(${c.id}, '${c.code}')">
                    <i class="fas fa-trash-alt"></i> Xoá
                </button>` : '';

                        html += `
                <tr>
                    <td class="ps-4">
                        <b class="text-primary d-block">${c.code}</b>
                        ${staffName}
                    </td>
                    <td>${username}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            ${blockBtn}
                            ${deleteBtn}
                        </div>
                    </td>
                </tr>
            `;
                    });
                    tbody.html(html || '<tr><td colspan="4" class="text-center py-4">Trống</td></tr>');
                });
            }

            // Hàm Xóa (Thu hồi) mã
            window.deleteRegCode = function(id, codeString) {
                showConfirmDialog({
                    title: 'Thu hồi mã?',
                    text: `Bạn có chắc chắn muốn xóa mã "${codeString}" không? Mã này sẽ không thể dùng để đăng ký được nữa.`,
                    confirmButtonText: 'Đồng ý thu hồi',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/registration-codes/${id}`,
                            type: 'DELETE',
                            success: function(res) {
                                showToast('Đã thu hồi mã thành công!', 'success', 'Đã xóa');
                                loadRegistrationCodes(); // Tải lại bảng ngay lập tức
                            },
                            error: function(xhr) {
                                showToast('Lỗi khi xóa mã. Vui lòng thử lại.', 'error', 'Thất bại');
                            }
                        });
                    }
                });
            }
            // Hàm xử lý Khóa/Mở khóa qua AJAX
            window.toggleBlockRegCode = function(id) {
                $.post(`/registration-codes/${id}/toggle-block`, {
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    showToast(res.message, 'success', 'Thành công');
                    loadRegistrationCodes();
                });
            }
        </script>
    @endpush
@endif
