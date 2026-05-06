<!-- resources/views/tuitions/_modal_payment.blade.php -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> Thanh toán Học phí</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="paymentForm" action="{{ url('/tuitions/pay') }}" method="POST">
                @csrf
                <input type="hidden" id="pay_tuition_id" name="tuition_id">

                <div class="modal-body row">
                    <!-- Cột Trái: Thông tin nhập liệu -->
                    <div class="col-md-7 border-end">
                        <h6 class="fw-bold text-primary mb-3">Học viên: <span id="pay_student_name"
                                class="text-dark"></span></h6>

                        <!-- Chỉ sửa phần chọn Gói Học phí thành Chọn Số Tuần -->
                        <input type="hidden" id="course_price_per_week" value="0">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thời lượng đóng phí (Tuần) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" id="pay_weeks" name="paid_weeks" required>
                                    <option value="">-- Chọn số tuần --</option>
                                    <option value="8">8 Tuần</option>
                                    <option value="16">16 Tuần</option>
                                    <option value="32">32 Tuần</option>
                                    <option value="48">48 Tuần (Gần 1 năm)</option>
                                </select>
                                <span class="input-group-text bg-light text-muted"
                                    id="display_price_per_week">0đ/tuần</span>
                            </div>
                        </div>

                        <!-- Chọn Khuyến mãi -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Khuyến mãi áp dụng</label>
                            <select class="form-select" id="pay_promotion" name="promotion_id">
                                <option value="" data-discount="0">-- Không áp dụng --</option>
                                @foreach (\App\Models\Promotion::where('is_active', true)->get() as $promo)
                                    <option value="{{ $promo->id }}" data-discount="{{ $promo->discount_percent }}">
                                        {{ $promo->name }} (Giảm {{ $promo->discount_percent }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hình thức thanh toán <span
                                    class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="method_cash" value="cash" checked>
                                    <label class="form-check-label" for="method_cash"><i
                                            class="fas fa-money-bill-wave text-success"></i> Tiền mặt</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="method_qr"
                                        value="vietqr">
                                    <label class="form-check-label" for="method_qr"><i
                                            class="fas fa-qrcode text-primary"></i> Chuyển khoản (VietQR)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột Phải: Tổng tiền & Mã QR -->
                    <div class="col-md-5 text-center bg-light rounded-3 p-3">
                        <h6 class="fw-bold text-muted mb-3">TỔNG TIỀN THANH TOÁN</h6>
                        <h2 class="text-danger fw-bold mb-2" id="display_final_amount">0đ</h2>
                        <div class="text-muted small mb-4">
                            Giá gốc: <del id="display_original_amount">0đ</del>
                        </div>

                        <!-- Khu vực VietQR (Sẽ ẩn nếu chọn Tiền mặt) -->
                        <div id="vietqr_area" style="display: none;">
                            <p class="fw-bold text-primary mb-1">Quét mã để thanh toán</p>
                            <img id="vietqr_img" src="" alt="VietQR"
                                class="img-fluid border rounded shadow-sm" style="max-width: 200px;">
                            <p class="small text-muted mt-2 mb-0">Nội dung CK: <b id="vietqr_content"></b></p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <!-- Nút này chỉ được bấm khi Kế toán đã nhận được tiền (Tiền mặt hoặc Ting Ting) -->
                    <button type="submit" class="btn btn-primary fw-bold" id="btnConfirmPayment">Xác nhận Đã Thu Tiền &
                        Sinh Biên Lai</button>
                </div>
            </form>
        </div>
    </div>
</div>
