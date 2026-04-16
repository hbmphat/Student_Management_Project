<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo giá Khóa học EngBreak</title>
    <style>
        /* CSS dùng riêng cho bản in PDF */
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Font hỗ trợ tiếng Việt chuẩn cho PDF */
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0b3d91;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 { color: #0b3d91; margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 14px; color: #555; }
        
        .course-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 15px;
            page-break-inside: avoid; /* Chống bị cắt nửa bảng khi sang trang mới */
        }
        .course-title {
            font-size: 18px; color: #b22222; font-weight: bold; margin-top: 0;
            border-bottom: 1px dashed #ccc; padding-bottom: 5px;
        }
        
        /* Dùng Table để chia cột giá vì PDF cực kỳ thích Table */
        table.pricing {
            width: 100%; border-collapse: collapse; margin-top: 10px;
        }
        table.pricing th, table.pricing td {
            border: 1px solid #eee; padding: 8px; text-align: center; font-size: 13px;
        }
        table.pricing th { background-color: #f6d14d; color: #333; }
        
        .footer {
            margin-top: 30px; text-align: center; font-size: 12px; font-style: italic; color: #888;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $center_name }}</h1>
        <p><i>"{{ $slogan }}"</i></p>
        <p>📍 {{ $address }} | 📞 Hotline: <b>{{ $hotline }}</b></p>
        <p>Báo giá áp dụng từ ngày: {{ $date }}</p>
    </div>

    @foreach($courses as $course)
    <div class="course-box">
        <h3 class="course-title">📚 {{ $course->name }} (Độ dài chuẩn: {{ $course->duration_months }} tháng)</h3>
        <p style="font-size: 13px; margin-bottom: 5px;"><i>Học phí cơ bản: {{ number_format($course->weekly_price, 0, ',', '.') }} VNĐ / tuần</i></p>
        
        <table class="pricing">
            <thead>
                <tr>
                    <th>Gói Thanh Toán</th>
                    <th>Thời gian học</th>
                    <th>Tổng Học Phí (VNĐ)</th>
                    <th>Ưu đãi (Gợi ý)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>Gói Ngắn hạn</b></td>
                    <td>8 Tuần</td>
                    <td style="color: red; font-weight: bold;">
                        {{ number_format($course->weekly_price * 8, 0, ',', '.') }} đ
                    </td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><b>Gói Tiêu chuẩn</b></td>
                    <td>16 Tuần</td>
                    <td style="color: red; font-weight: bold;">
                        {{ number_format($course->weekly_price * 16, 0, ',', '.') }} đ
                    </td>
                    <td>Tặng giáo trình</td>
                </tr>
                <tr>
                    <td><b>Gói Chuyên sâu</b></td>
                    <td>32 Tuần</td>
                    <td style="color: red; font-weight: bold;">
                        {{ number_format($course->weekly_price * 32, 0, ',', '.') }} đ
                    </td>
                    <td>Giảm 5% + Balô</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="footer">
        Cảm ơn quý khách đã quan tâm đến dịch vụ của {{ $center_name }}.<br>
        Vui lòng mang theo bản in này hoặc chụp màn hình khi đến trung tâm đăng ký để được hỗ trợ nhanh nhất.
    </div>

</body>
</html>