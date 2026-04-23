@push('scripts')
    <script>
        function openAddClassModal() {
            $('#form-add-class')[0].reset();
            $('#addClassModal').modal('show');
        }

        $('#form-add-class').submit(function(e) {
            e.preventDefault();
            let btn = $('#btn-save-class');
            btn.prop('disabled', true).text('Đang xử lý...');

            $.ajax({
                url: "{{ route('class-rooms.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    $('#addClassModal').modal('hide');
                    showToast(response.message, 'success', 'Thành công').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Lưu & Tạo Lớp');
                    showToast('Vui lòng điền đầy đủ thông tin.', 'error', 'Thất bại');
                }
            });
        });

        let currentClassId = null;

        function viewClass(id) {
            currentClassId = id;
            $('#viewClassModal').modal('show');

            $('.edit-day-checkbox').prop('checked', false);
            $('#detail-class-name').text('Đang tải thông tin...');

            $.ajax({
                url: `/class-rooms/${id}`,
                type: 'GET',
                success: function(res) {
                    let classroom = res.classRoom;

                    $('#detail-class-name').text(classroom.name);
                    $('#edit-course-id').val(classroom.course_id);
                    $('#edit-teacher-id').val(classroom.teacher_id);
                    $('#edit-shift-id').val(classroom.shift_id);
                    $('#edit-room').val(classroom.room);
                    $('#detail-time').text(classroom.formatted_start_date + ' đến ' + classroom
                        .formatted_end_date);
                    $('#edit-start-date').val(classroom.input_start_date);
                    $('#edit-end-date').val(classroom.input_end_date);
                    $('#edit-status').val(classroom.status);
                    if (classroom.schedules && classroom.schedules.length > 0) {
                        classroom.schedules.forEach(item => {
                            $(`#edit_day_${item.day_of_week}`).prop('checked', true);
                        });
                    }
                    loadClassStudents(id);
                },
                error: function() {
                    showToast('Không thể lấy thông tin lớp học.', 'error', 'Thất bại');
                }
            });
        }

        //gửi form Update
        $('#form-update-class').submit(function(e) {
            e.preventDefault();

            showConfirmDialog({
                title: 'Xác nhận cập nhật?',
                text: 'Hệ thống sẽ tính toán lại toàn bộ lịch học sắp tới của lớp này!',
                icon: 'question',
                confirmButtonText: 'Cập nhật'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/class-rooms/${currentClassId}`,
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(res) {
                            showToast(res.message, 'success', 'Thành công').then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            showToast('Vui lòng kiểm tra lại dữ liệu nhập vào.', 'error', 'Thất bại');
                        }
                    });
                }
            });
        });

        function deleteClass() {
            showConfirmDialog({
                title: 'Xóa lớp học này?',
                text: 'Lớp sẽ bị đưa vào thùng rác!',
                confirmButtonText: 'Xóa ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/class-rooms/${currentClassId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            showToast(res.message, 'success', 'Đã xóa').then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }
        $('#form-add-shift').submit(function(e) {
            e.preventDefault();
            let btn = $('#btn-save-shift');
            btn.prop('disabled', true).text('Đang lưu...');

            $.ajax({
                url: "{{ route('shifts.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    let newRow = `
                <tr id="shift-row-${res.shift.id}">
                    <td class="fw-bold text-dark">${res.shift.name}</td>
                    <td><span class="badge bg-info text-dark fs-6">${res.formatted_time}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteShift(${res.shift.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                    if ($('#shift-list-body td').length == 1) {
                        $('#shift-list-body').empty();
                    }

                    $('#shift-list-body').append(newRow);
                    $('select[name="shift_id"]').append(
                        `<option value="${res.shift.id}">${res.shift.name}</option>`);
                    $('#form-add-shift')[0].reset();
                    btn.prop('disabled', false).text('Thêm Ca');

                    showToast('Đã thêm ca học thành công!', 'success', 'Thành công');
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Thêm Ca');
                    showToast('Vui lòng kiểm tra lại thông tin (Giờ kết thúc phải lớn hơn giờ bắt đầu).', 'error', 'Thất bại');
                }
            });
        });

        function deleteShift(id) {
            showConfirmDialog({
                title: 'Xóa ca học này?',
                text: 'Nếu ca này đang có lớp học, bạn sẽ không thể xóa!',
                confirmButtonText: 'Xóa ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/shifts/${id}`,
                        type: "DELETE",
                        success: function(res) {
                            $(`#shift-row-${id}`).fadeOut();
                            $(`select[name="shift_id"] option[value="${id}"]`).remove();
                            showToast(res.message, 'success', 'Đã xóa');
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON.message, 'error', 'Thất bại');
                        }
                    });
                }
            });
        }
        // hàm load danh sách học viên của lớp
        function loadClassStudents(classId) {
            $.get(`/class-rooms/${classId}/students`, function(res) {
                let html = '';
                $('#detail-student-count').text(res.students.length);

                if (res.students.length === 0) {
                    html = '<tr><td colspan="4" class="text-center text-muted">Chưa có học viên.</td></tr>';
                } else {
                    res.students.forEach((s, index) => {
                        html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><b>${s.uuid}</b> - ${s.name}</td>
                    <td><span class="badge bg-success small">Đang học</span></td>
                    <td>
                        <button type="button" class="btn btn-sm text-danger" onclick="removeStudentFromClass(${s.id})">
                            <i class="fas fa-times">Xoá</i>
                        </button>
                    </td>
                </tr>`;
                    });
                }
                $('#student-list-body').html(html);
            });
        }

        // xử lý tìm kiếm học viên để thêm
        $('#search-student-to-add').on('keyup', function() {
            let q = $(this).val();
            if (q.length < 2) {
                $('#search-results').addClass('d-none');
                return;
            }

            $.get(`/class-rooms/${currentClassId}/search-students`, {
                q: q
            }, function(data) {
                let html = '';
                data.forEach(s => {
                    html += `<a href="javascript:void(0)" class="list-group-item list-group-item-action small" onclick="addStudentToClass(${s.id})">
                <i class="fas fa-plus-circle text-success me-2"></i> ${s.uuid} - ${s.name}
            </a>`;
                });
                $('#search-results').html(html).removeClass('d-none');
            });
        });

        // Hàm thêm học viên
        function addStudentToClass(studentId) {
            $.post(`/class-rooms/${currentClassId}/add-student`, {
                _token: '{{ csrf_token() }}',
                student_id: studentId
            }, function(res) {
                // 1. Ẩn kết quả tìm kiếm và xóa ô nhập
                $('#search-results').addClass('d-none');
                $('#search-student-to-add').val('');

                // 2. Hiện thông báo nhỏ góc màn hình (Toast)
                showToast('Đã thêm học viên vào lớp!', 'success', 'Thành công');

                // 3. Tải lại danh sách học viên
                loadClassStudents(currentClassId);
            }).fail(function() {
                showToast('Không thể thêm học viên này!', 'error', 'Thất bại');
            });
        }

        // hàm xóa học viên khỏi lớp
        function removeStudentFromClass(studentId) {
            showConfirmDialog({
                title: 'Xóa học viên khỏi lớp?',
                text: 'Học viên này sẽ bị gỡ khỏi danh sách lớp hiện tại!',
                confirmButtonText: 'Xóa ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/class-rooms/${currentClassId}/students/${studentId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            // Hiện thông báo nhỏ góc màn hình (Toast)
                            showToast(res.message || 'Đã xóa học viên khỏi lớp!', 'success', 'Đã xóa');

                            // Tải lại danh sách học viên
                            loadClassStudents(currentClassId);
                        },
                        error: function(xhr) {
                            showToast('Không thể xóa học viên này.', 'error', 'Thất bại');
                        }
                    });
                }
            });
        }
    </script>
@endpush
