@push('scripts')
<script>
window.startBackup = function(e) {
    e.preventDefault();
    $('#backup_confirm_password').val('').removeClass('is-invalid');
    $('#backup_password_error').text('');
    showBootstrapModal('#backupModal');
}

$('#backupForm').on('submit', function(e) {
    e.preventDefault();

    const $button = $('#btnBackupConfirm');
    const originalButtonHtml = $button.html();

    $.ajax({
        url: "{{ route('backup.download') }}",
        method: 'POST',
        data: {
            password: $('#backup_confirm_password').val()
        },
        dataType: 'json',
        headers: {
            Accept: 'application/json'
        },
        beforeSend: function() {
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');
        },
        success: function(response) {
            hideBootstrapModal('#backupModal');
            const isFullBackup = response.backup_mode === 'full';
            showToast(
                response.message || 'Backup dữ liệu thành công.',
                isFullBackup ? 'success' : 'warning',
                isFullBackup ? 'Thành công' : 'Hoàn tất một phần'
            );

            if (response.download_url) {
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = `${response.download_url}?t=${Date.now()}`;
                document.body.appendChild(iframe);

                setTimeout(() => {
                    iframe.remove();
                }, 60000);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                $('#backup_confirm_password').addClass('is-invalid');
                $('#backup_password_error').text(xhr.responseJSON?.message || 'Mật khẩu không đúng.');
                return;
            }

            showToast(xhr.responseJSON?.message || 'Hệ thống đang bận, vui lòng thử lại.', 'error', 'Thất bại');
        },
        complete: function() {
            $button.prop('disabled', false).html(originalButtonHtml);
        }
    });
});
</script>
@endpush