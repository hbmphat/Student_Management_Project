@extends('layouts.master')
@section('title', 'Quản lý Học phí | ENGBREAK')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary"><i class="fas fa-file-invoice-dollar me-2"></i> Danh sách Học phí Học viên</h2>
            <!-- Nút thêm Khuyến mãi (Sẽ làm sau) -->
            <button class="btn btn-primary" onclick="openListPromotionsModal()">
                <i class="fas fa-tags"></i> Quản lý Khuyến mãi
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Học viên</th>
                                <th>Lớp học</th>
                                <th>Thời hạn học phí</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tuitions as $t)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <!-- Giả sử bạn có hàm display_avatar trong model Student -->
                                            <img src="{{ $t->student->display_avatar ?? asset('images/default-avatar.png') }}"
                                                class="rounded-circle me-3"
                                                style="width: 45px; height: 45px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $t->student->name }}</div>
                                                <div class="text-muted small"><i class="fas fa-id-badge"></i>
                                                    {{ $t->student->uuid }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ $t->classRoom->name }}</span>
                                    </td>
                                    <td>
                                        @if ($t->from_date && $t->to_date)
                                            <div class="fw-bold text-dark">
                                                {{ \Carbon\Carbon::parse($t->from_date)->format('d/m/Y') }}
                                                <i class="fas fa-arrow-right mx-1 text-muted">-</i>
                                                {{ \Carbon\Carbon::parse($t->to_date)->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">Chưa có dữ liệu đóng phí</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <!-- Trạng thái hiển thị theo Carbon tính toán ở Controller -->
                                        <span
                                            class="badge bg-{{ $t->status_color }} {{ $t->status_color == 'warning' ? 'text-dark' : '' }} p-2">
                                            {{ $t->status_text }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- LUỒNG 1: Thanh toán (Sinh biên lai) -->
                                        <button class="btn btn-sm btn-primary fw-bold mb-1"
                                            onclick="openPaymentModal({{ $t->id }}, '{{ $t->student->name }}', '{{ $t->student->uuid }}', {{ $t->classRoom->course->weekly_price ?? 0 }})">
                                            <i class="fas fa-money-bill-wave"></i> Thanh toán
                                        </button>

                                        <!-- LUỒNG 2: Gia hạn (Áp mã biên lai) -->
                                        <button class="btn btn-sm btn-success fw-bold mb-1"
                                            onclick="openExtendModal({{ $t->id }}, '{{ $t->student->name }}')"
                                            title="Gia hạn học phí bằng Biên lai">
                                            <i class="fas fa-calendar-plus"></i> Gia hạn
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu học phí nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $tuitions->links() }}
                </div>
            </div>
        </div>
    </div>
    @include('tuitions._modal_payment')
    @include('tuitions._modal_promotions')
    @include('tuitions._modal_extend')
    @push('scripts')
        <script>
            // Cập nhật hàm mở Modal nhận thêm giá 1 tuần
            window.openPaymentModal = function(tuitionId, studentName, studentUuid, pricePerWeek) {
                $('#paymentForm')[0].reset();
                $('#pay_tuition_id').val(tuitionId);
                $('#pay_student_name').text(studentName);
                $('#course_price_per_week').val(pricePerWeek); // Lưu giá tuần vào thẻ hidden

                // Hiển thị giá tuần cho Kế toán dễ nhìn
                $('#display_price_per_week').text(new Intl.NumberFormat('vi-VN').format(pricePerWeek) + 'đ/tuần');

                currentStudentUuid = studentUuid;
                currentStudentNName = studentName;
                calculateAmount();
                $('#vietqr_area').hide();
                showBootstrapModal('#paymentModal');
            }

            // Tính toán lại
            $('#pay_weeks, #pay_promotion, input[name="payment_method"]').change(function() {
                calculateAmount();
            });

            window.openExtendModal = function(tuitionId, studentName) {
                $('#extendForm')[0].reset();
                $('#extend_tuition_id').val(tuitionId);
                $('#extend_student_name').text(studentName);
                $('#extend_receipt_code').val('');
                showBootstrapModal('#extendModal');
            }

            $('#extendForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $button = $('#btnConfirmExtend');
                const originalButtonHtml = $button.html();

                $.ajax({
                    url: $form.attr('action') || '{{ url('/tuitions/extend') }}',
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    headers: {
                        Accept: 'application/json'
                    },
                    beforeSend: function() {
                        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');
                    },
                    success: function(response) {
                        hideBootstrapModal('#extendModal');
                        showToast(response.message || 'Gia hạn thành công.', 'success', 'Thành công');
                        window.location.reload();
                    },
                    error: function(xhr) {
                        let message = 'Gia hạn thất bại.';

                        if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }

                        showToast(message, 'error', 'Thất bại');
                    },
                    complete: function() {
                        $button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $button = $('#btnConfirmPayment');
                const originalButtonHtml = $button.html();
                const receiptWindow = window.open('', '_blank');

                if (receiptWindow) {
                    receiptWindow.document.write('<p style="font-family:sans-serif;padding:20px">Đang tạo biên lai...</p>');
                }

                $.ajax({
                    url: $form.attr('action') || '{{ url('/tuitions/pay') }}',
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    headers: {
                        Accept: 'application/json'
                    },
                    beforeSend: function() {
                        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');
                    },
                    success: function(response) {
                        hideBootstrapModal('#paymentModal');

                        showToast(response.message || 'Thanh toán thành công.', 'success', 'Thành công');
                        if (response.receipt_code) {
                            const receiptUrl = '/tuitions/receipt/' + response.receipt_code;
                            if (receiptWindow && !receiptWindow.closed) {
                                receiptWindow.location = receiptUrl;
                                receiptWindow.focus();
                            } else {
                                window.open(receiptUrl, '_blank');
                            }
                        }
                        window.location.reload();
                    },
                    error: function(xhr) {
                        let message = 'Thanh toán thất bại.';

                        if (receiptWindow && !receiptWindow.closed) {
                            receiptWindow.close();
                        }

                        if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }

                        showToast(message, 'error', 'Thất bại');
                    },
                    complete: function() {
                        $button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            function calculateAmount() {
                let weeks = parseInt($('#pay_weeks').val()) || 0;
                let pricePerWeek = parseFloat($('#course_price_per_week').val()) || 0;
                let promoOption = $('#pay_promotion option:selected');
                let discountPercent = promoOption.data('discount') || 0;

                // THE MAGIC TRICK LÀ Ở ĐÂY:
                let originalPrice = weeks * pricePerWeek;
                let discountAmount = originalPrice * (discountPercent / 100);
                let finalAmount = originalPrice - discountAmount;

                $('#display_original_amount').text(new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ');
                $('#display_final_amount').text(new Intl.NumberFormat('vi-VN').format(finalAmount) + 'đ');

                // Logic VietQR giữ nguyên... (Nhớ check finalAmount > 0)
                let method = $('input[name="payment_method"]:checked').val();
                if (method === 'vietqr' && finalAmount > 0) {
                    let bankBin = '970423';
                    let bankAccount = '07564271147';
                    let transferContent = `HP ${currentStudentUuid} ${currentStudentNName} ${weeks}Tuần`;
                    let qrUrl =
                        `https://img.vietqr.io/image/${bankBin}-${bankAccount}-compact.png?amount=${finalAmount}&addInfo=${transferContent}&accountName=ENGBREAK`;

                    $('#vietqr_img').attr('src', qrUrl);
                    $('#vietqr_content').text(transferContent);
                    $('#vietqr_area').fadeIn();
                } else {
                    $('#vietqr_area').hide();
                }
            }
        </script>
    @endpush
@endsection
