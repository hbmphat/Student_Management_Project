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
                    Swal.fire('Thành công!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Lưu & Tạo Lớp');
                    Swal.fire('Lỗi!', 'Vui lòng điền đầy đủ thông tin.', 'error');
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
                    $('#detail-time').text(classroom.formatted_start_date + ' đến ' + classroom.formatted_end_date);
                    $('#edit-start-date').val(classroom.input_start_date);
                    $('#edit-end-date').val(classroom.input_end_date);
                    $('#edit-status').val(classroom.status);
                    if (classroom.schedules && classroom.schedules.length > 0) {
                        classroom.schedules.forEach(item => {
                            $(`#edit_day_${item.day_of_week}`).prop('checked', true);
                        });
                    }
                    $('#detail-student-count').text(0);
                    $('#student-list-body').html(
                        '<tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu học viên.</td></tr>'
                    );
                },
                error: function() {
                    Swal.fire('Lỗi!', 'Không thể lấy thông tin lớp học.', 'error');
                }
            });
        }

        //gửi form Update
        $('#form-update-class').submit(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Xác nhận cập nhật?',
                text: "Hệ thống sẽ tính toán lại toàn bộ lịch học sắp tới của lớp này!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/class-rooms/${currentClassId}`,
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(res) {
                            Swal.fire('Thành công!', res.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Lỗi!', 'Vui lòng kiểm tra lại dữ liệu nhập vào.',
                                'error');
                        }
                    });
                }
            });
        });
        function deleteClass() {
            Swal.fire({
                title: 'Xóa lớp học này?',
                text: "Lớp sẽ bị đưa vào thùng rác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/class-rooms/${currentClassId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire('Đã xóa!', res.message, 'success').then(() => {
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

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        title: 'Đã thêm ca học thành công!'
                    });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Thêm Ca');
                    Swal.fire('Lỗi',
                        'Vui lòng kiểm tra lại thông tin (Giờ kết thúc phải lớn hơn giờ bắt đầu).',
                        'error');
                }
            });
        });

        function deleteShift(id) {
            Swal.fire({
                title: 'Xóa ca học này?',
                text: "Nếu ca này đang có lớp học, bạn sẽ không thể xóa!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa ngay!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/shifts/${id}`,
                        type: "DELETE",
                        success: function(res) {
                            $(`#shift-row-${id}`).fadeOut();
                            $(`select[name="shift_id"] option[value="${id}"]`).remove();
                            Swal.fire('Đã xóa!', res.message, 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
