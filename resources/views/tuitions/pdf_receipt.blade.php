<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Biên Lai Học Phí - {{ $payment->receipt_code }}</title>
    <style>
        /* Font DejaVu Sans hỗ trợ chuẩn Tiếng Việt UTF-8 */
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 120px; float: left; }
        .center-info { float: left; margin-left: 20px; }
        .center-info h2 { margin: 0; color: #0d6efd; font-size: 20px; }
        .center-info p { margin: 3px 0; }
        .clear { clear: both; }
        .receipt-title { text-align: center; font-size: 22px; font-weight: bold; margin: 20px 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .data-table th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { width: 100%; margin-top: 30px; }
        .signature-box { width: 33%; float: left; text-align: center; }
        .text-danger { color: #dc3545; }
    </style>
</head>
<body>

    <div class="header">
        <!-- Đọc logo từ thư mục public/images/logo.png -->
        <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
        <div class="center-info">
            <h2>HỆ THỐNG ANH NGỮ ENGBREAK</h2>
            <p><strong>Địa chỉ:</strong> Số 123, Đường ABC, TP. XYZ</p>
            <p><strong>Hotline:</strong> 0987.654.321</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="receipt-title">BIÊN LAI THU TIỀN HỌC PHÍ</div>

    <table class="info-table">
        <tr>
            <td width="60%"><strong>Mã biên lai:</strong> {{ $payment->receipt_code }}</td>
            <td width="40%"><strong>Ngày lập:</strong> {{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Học viên:</strong> {{ $payment->student->name }} (Mã: {{ $payment->student->uuid }})</td>
            <td><strong>Hình thức:</strong> {{ $payment->payment_method == 'cash' ? 'Tiền mặt' : 'Chuyển khoản (VietQR)' }}</td>
        </tr>
        <tr>
            <td><strong>Lớp học:</strong> {{ $payment->classRoom->name }} (Khóa: {{ $payment->classRoom->course->name ?? 'N/A' }})</td>
            <td><strong>Thu ngân:</strong> {{ $payment->creator->name }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Nội dung đóng phí</th>
                <th class="text-center">Số lượng</th>
                <th class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Gói học phí theo tuần</td>
                <td class="text-center">{{ $payment->paid_weeks }} Tuần</td>
                <td class="text-right">{{ number_format($payment->original_amount) }} đ</td>
            </tr>
            @if($payment->discount_amount > 0)
            <tr>
                <td colspan="3" class="text-right"><strong>Khuyến mãi ({{ $payment->promotion->name ?? 'Giảm giá' }}):</strong></td>
                <td class="text-right">- {{ number_format($payment->discount_amount) }} đ</td>
            </tr>
            @endif
            <tr>
                <td colspan="3" class="text-right"><strong>TỔNG TIỀN THANH TOÁN:</strong></td>
                <td class="text-right text-danger" style="font-size: 18px; font-weight: bold;">{{ number_format($payment->final_amount) }} đ</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <strong>Người nộp tiền</strong><br>
            <span style="font-size: 11px; font-style: italic;">(Ký, ghi rõ họ tên)</span>
        </div>
        <div class="signature-box">
            <strong>Giám đốc trung tâm</strong><br>
            <span style="font-size: 11px; font-style: italic;">(Ký, đóng dấu)</span>
        </div>
        <div class="signature-box">
            <strong>Người thu tiền</strong><br>
            <span style="font-size: 11px; font-style: italic;">(Ký, ghi rõ họ tên)</span><br><br><br><br>
            {{ $payment->creator->name }}
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>