@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // 2. THIẾT LẬP CSRF TOKEN CHO AJAX (Sửa lỗi 419 khi thêm/sửa/xóa)
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let isEdit = false;

    function openAddModal() {
        isEdit = false;
        $('#teacherForm')[0].reset();
        $('#teacher_id').val('');
        $('#status').val('available'); // Reset về mặc định
        $('#modalTitle').text('Thêm Giảng Viên Mới');
        $('#errorList').addClass('d-none').html('');
        $('#teacherModal').modal('show');
    }

    function openEditModal(id) {
        isEdit = true;
        $('#errorList').addClass('d-none').html('');
        
        $.get(`/teachers/${id}/edit`, function(data) {
            $('#teacher_id').val(data.id);
            $('#teacher_code').val(data.teacher_code);
            $('#name').val(data.name);
            $('#phone').val(data.phone);
            $('#email').val(data.email);
            $('#status').val(data.status);
            $('#qualifications').val(data.qualifications);
            
            $('#modalTitle').text('Sửa Hồ Sơ Giảng Viên');
            $('#teacherModal').modal('show');
        });
    }

    $('#teacherForm').submit(function(e) {
        e.preventDefault();
        
        let id = $('#teacher_id').val();
        let url = isEdit ? `/teachers/${id}` : '/teachers';
        let method = isEdit ? 'PUT' : 'POST';
        
        // Thu thập dữ liệu
        let data = {
            teacher_code: $('#teacher_code').val(),
            name: $('#name').val(),
            phone: $('#phone').val(),
            email: $('#email').val(),
            status: $('#status').val(),
            qualifications: $('#qualifications').val(),
        };

        $('#btnSave').prop('disabled', true).text('Đang lưu...');

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                $('#teacherModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: response.success,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                $('#btnSave').prop('disabled', false).text('Lưu dữ liệu');
                let errors = xhr.responseJSON.errors;
                let errorHtml = '';
                $.each(errors, function(key, value) {
                    errorHtml += `<li>${value[0]}</li>`;
                });
                $('#errorList').removeClass('d-none').html(errorHtml);
            }
        });
    });

    function deleteTeacher(id) {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Hồ sơ giảng viên sẽ được chuyển vào thùng rác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Đồng ý xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/teachers/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Đã xóa!', response.success, 'success');
                        $(`#row-${id}`).fadeOut(500, function() { $(this).remove(); });
                    }
                });
            }
        })
    }
</script>
@endpush