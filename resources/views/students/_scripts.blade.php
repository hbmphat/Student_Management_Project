@push('scripts')
    <script>
        // Bọc toàn bộ code trong document.ready để đảm bảo thư viện jQuery đã tải xong
        $(document).ready(function() {
            let currentStudentId = null;

            // 1. Mở Modal Thêm
            window.openAddStudentModal = function() {
                $('#form-add-student')[0].reset();
                $('#addStudentModal').modal('show');
            }

            // 2. Thêm Học viên (Hỗ trợ Upload File)
            $('#form-add-student').submit(function(e) {
                e.preventDefault(); // Chặn load trang (Rất quan trọng)
                let btn = $('#btn-save-student');
                btn.prop('disabled', true).text('Đang lưu...');

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('students.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#addStudentModal').modal('hide');
                        showToast(res.message, 'success', 'Thành công').then(() => location.reload());
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Lưu Thông Tin');
                        showToast('Vui lòng kiểm tra lại dữ liệu nhập vào.', 'error', 'Thất bại');
                    }
                });
            });

            // 3. Mở Modal Chi tiết & Đổ dữ liệu
            window.viewStudent = function(id) {
                currentStudentId = id;
                $('#viewStudentModal').modal('show');
                $('#header-student-name').text('Đang tải...');

                $.get(`/students/${id}`, function(res) {
                    let s = res.student;

                    $('#header-student-name').text(s.name);
                    $('#detail-name').text(s.name);
                    $('#detail-uuid').text(s.uuid);
                    $('#detail-avatar').attr('src', s.display_avatar);
                    $('#btn-print-card').attr('href', `/students/${s.id}/print-card`);

                    $('#edit-name').val(s.name);
                    $('#edit-dob').val(s.dob.split('T')[0]);
                    $('#edit-gender').val(s.gender);
                    $('#edit-phone').val(s.parent_phone);
                    $('#edit-email').val(s.parent_email);
                    $('#edit-address').val(s.address);
                    $('#edit-status').val(s.status);

                    $('#edit-street-address').val(s.street_address);

                    // Đổ địa chỉ 3 cấp (Dùng cơ chế callback/trigger)
                    if (s.ward && s.ward.district) {
                        $('#edit-province-id').val(s.ward.district.province_id);

                        // Load Huyện và tự động chọn huyện hiện tại, sau đó load tiếp Xã
                        loadDistricts(s.ward.district.province_id, '#edit-district-id', '#edit-ward-id',
                            s.ward.district_id);

                        // Để đảm bảo Xã được chọn đúng sau khi Huyện load xong, ta cần một chút delay hoặc xử lý đồng bộ
                        setTimeout(() => {
                            loadWards(s.ward.district_id, '#edit-ward-id', s.ward_id);
                        }, 500);
                    }
                });
            }
            // A. XỬ LÝ SỰ KIỆN CHANGE CHO MODAL SỬA (Tương tự modal thêm)
            $('#edit-province-id').change(function() {
                loadDistricts($(this).val(), '#edit-district-id', '#edit-ward-id');
            });

            $('#edit-district-id').change(function() {
                loadWards($(this).val(), '#edit-ward-id');
            });

            // Hàm dùng chung để load Huyện
            function loadDistricts(provinceId, targetSelector, wardSelector, selectedId = null) {
                $(targetSelector).html('<option value="">Đang tải...</option>').prop('disabled', true);
                $(wardSelector).html('<option value="">-- Chọn Xã --</option>').prop('disabled', true);
                if (provinceId) {
                    $.get(`/api/provinces/${provinceId}/districts`, function(data) {
                        let html = '<option value="">-- Chọn Huyện --</option>';
                        data.forEach(d => html +=
                            `<option value="${d.id}" ${d.id == selectedId ? 'selected' : ''}>${d.name}</option>`
                            );
                        $(targetSelector).html(html).prop('disabled', false);
                        if (selectedId) $(targetSelector).trigger(
                        'change'); // Tiếp tục load Xã nếu có ID truyền vào
                    });
                }
            }

            // Hàm dùng chung để load Xã
            function loadWards(districtId, targetSelector, selectedId = null) {
                $(targetSelector).html('<option value="">Đang tải...</option>').prop('disabled', true);
                if (districtId) {
                    $.get(`/api/districts/${districtId}/wards`, function(data) {
                        let html = '<option value="">-- Chọn Xã --</option>';
                        data.forEach(w => html +=
                            `<option value="${w.id}" ${w.id == selectedId ? 'selected' : ''}>${w.name}</option>`
                            );
                        $(targetSelector).html(html).prop('disabled', false);
                    });
                }
            }
            // 4. Cập nhật Học viên
            $('#form-update-student').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `/students/${currentStudentId}`,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        showToast(res.message, 'success', 'Thành công').then(() => location.reload());
                    }
                });
            });

            // 5. Xóa mềm
            window.deleteStudent = function() {
                showConfirmDialog({
                    title: 'Chuyển vào thùng rác?',
                    text: 'Bạn có thể khôi phục học viên này sau.',
                    confirmButtonText: 'Chuyển vào thùng rác',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/students/${currentStudentId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                showToast(res.message, 'success', 'Đã xóa').then(() => location.reload());
                            }
                        });
                    }
                });
            }
            // --- XỬ LÝ ĐỊA CHỈ HÀNH CHÍNH 3 CẤP ---

            // Khi chọn Tỉnh/Thành -> Tải danh sách Quận/Huyện
            $('#province_id').change(function() {
                let provinceId = $(this).val();

                // Xóa dữ liệu cũ và hiện chữ "Đang tải"
                $('#district_id').html('<option value="">Đang tải...</option>').prop('disabled', true);
                $('#ward_id').html('<option value="">-- Chọn Phường/Xã --</option>').prop('disabled', true);

                if (provinceId) {
                    $.get(`/api/provinces/${provinceId}/districts`, function(data) {
                        let html = '<option value="">-- Chọn Quận/Huyện --</option>';
                        data.forEach(function(district) {
                            html +=
                                `<option value="${district.id}">${district.name}</option>`;
                        });
                        $('#district_id').html(html).prop('disabled', false); // Mở khóa Huyện
                    });
                }
            });

            // Khi chọn Quận/Huyện -> Tải danh sách Phường/Xã
            $('#district_id').change(function() {
                let districtId = $(this).val();

                $('#ward_id').html('<option value="">Đang tải...</option>').prop('disabled', true);

                if (districtId) {
                    $.get(`/api/districts/${districtId}/wards`, function(data) {
                        let html = '<option value="">-- Chọn Phường/Xã --</option>';
                        data.forEach(function(ward) {
                            html += `<option value="${ward.id}">${ward.name}</option>`;
                        });
                        $('#ward_id').html(html).prop('disabled', false); // Mở khóa Xã
                    });
                }
            });


        });
    </script>
@endpush
