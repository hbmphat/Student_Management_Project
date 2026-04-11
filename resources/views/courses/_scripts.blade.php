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

    // Mở Modal Thêm
    function openAddModal() {
        isEdit = false;
        $('#courseForm')[0].reset();
        $('#course_id').val('');
        $('#modalTitle').text('Thêm Khóa Học Mới');
        $('#errorList').addClass('d-none').html('');
        $('#courseModal').modal('show');
    }

    // Mở Modal Sửa (Gọi AJAX lấy data)
    function openEditModal(id) {
        isEdit = true;
        $('#errorList').addClass('d-none').html('');
        
        $.get(`/courses/${id}/edit`, function(data) {
            $('#course_id').val(data.id);
            $('#name').val(data.name);
            $('#duration_months').val(data.duration_months);
            $('#weekly_price').val(parseFloat(data.weekly_price));
            
            $('#modalTitle').text('Sửa Khóa Học');
            $('#courseModal').modal('show');
        });
    }

    // Xử lý Lưu (Submit Form)
    $('#courseForm').submit(function(e) {
        e.preventDefault();
        
        let id = $('#course_id').val();
        let url = isEdit ? `/courses/${id}` : '/courses';
        let method = isEdit ? 'PUT' : 'POST';
        let data = {
            name: $('#name').val(),
            duration_months: $('#duration_months').val(),
            weekly_price: $('#weekly_price').val(),
        };

        $('#btnSave').prop('disabled', true).text('Đang lưu...');

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                $('#courseModal').modal('hide');
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

    // Xóa khóa học
    function deleteCourse(id) {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Khoá học sẽ được chuyển vào thùng rác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Đồng ý xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/courses/${id}`,
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