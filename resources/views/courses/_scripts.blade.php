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

    function showBootstrapModal(selector) {
        const element = document.querySelector(selector);

        if (!element) {
            return;
        }

        bootstrap.Modal.getOrCreateInstance(element).show();
    }

    function hideBootstrapModal(selector) {
        const element = document.querySelector(selector);

        if (!element) {
            return;
        }

        bootstrap.Modal.getOrCreateInstance(element).hide();
    }

    // Mở Modal Thêm
    function openAddModal() {
        isEdit = false;
        $('#courseForm')[0].reset();
        $('#course_id').val('');
        $('#modalTitle').text('Thêm Khóa Học Mới');
        $('#errorList').addClass('d-none').html('');
        showBootstrapModal('#courseModal');
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
            $('#description').val(data.description);
            $('#modalTitle').text('Sửa Khóa Học');
            showBootstrapModal('#courseModal');
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
            description: $('#description').val()
        };

        $('#btnSave').prop('disabled', true).text('Đang lưu...');

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                hideBootstrapModal('#courseModal');
                showToast(response.success, 'success', 'Thành công').then(() => {
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
                showToast('Vui lòng kiểm tra lại dữ liệu nhập vào.', 'error', 'Thất bại');
            }
        });
    });

    // Xóa khóa học
    function deleteCourse(id) {
        showConfirmDialog({
            title: 'Xóa khóa học này?',
            text: 'Khóa học sẽ được chuyển vào thùng rác!',
            confirmButtonText: 'Xóa ngay'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/courses/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        showToast(response.success, 'success', 'Đã xóa');
                        $(`#row-${id}`).fadeOut(500, function() { $(this).remove(); });
                    }
                });
            }
        })
    }
</script>
@endpush