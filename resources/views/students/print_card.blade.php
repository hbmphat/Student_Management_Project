<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0px;
            size: 250pt 160pt;
        }

        body,
        html {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0px;
            padding: 0px;
        }

        .card-container {
            width: 244pt;
            /* TRẢ LẠI 244pt ĐỂ ÔM KHÍT TRANG IN */
            height: 150pt;
            /* TRẢ LẠI 153pt ĐỂ ÔM KHÍT TRANG IN */
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            background: #ffffff;
            border: 2px solid #000000;
        }

        .page-break {
            page-break-before: always;
        }

        /* LOGO MỜ Ở GIỮA (WATERMARK) */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 130pt;
            margin-top: -65pt;
            margin-left: -65pt;
            opacity: 0.07;
            z-index: 0;
        }

        .header {
            background: #0b3d91;
            color: white;
            padding: 5px;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .logo-header {
            position: absolute;
            left: 8px;
            top: 2px;
            height: 25px;
        }

        .content {
            display: table;
            width: 100%;
            padding: 10px;
            position: relative;
            z-index: 10;
        }

        .avatar {
            width: 2.1cm;
            height: 2.6cm;
            border: 1px solid #ccc;
            object-fit: cover;
        }

        .info {
            padding-left: 10px;
            font-size: 10px;
            line-height: 1.4;
        }

        .student-name {
            font-weight: bold;
            color: #0b3d91;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: #f4f6fa;
            font-size: 8px;
            text-align: center;
            padding: 3px;
            z-index: 10;
        }

        .back {
            text-align: center;
            padding-top: 10px;
        }

        .qr-section {
            margin-top: 5px;
            position: relative;
            z-index: 10;
        }

        .notes {
            font-size: 8px;
            text-align: left;
            padding: 2px;
            color: #555;
            position: relative;
            z-index: 10;
        }
    </style>
</head>

<body>
    <div class="card-container">
        <img src="{{ public_path('images/logo.png') }}" class="watermark">

        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" class="logo-header">
            <span style="font-size: 11px; font-weight: bold; display: block; text-align: center;">TRUNG TÂM NGOẠI NGỮ
                ENGBREAK</span>
        </div>

        <div class="content">
            <div style="display: table-cell; width: 2.3cm;">
                <img src="{{ $student['avatar'] }}" class="avatar">
            </div>
            <div style="display: table-cell; vertical-align: top; padding-left: 5px;">
                <div class="info">
                    <div class="student-name">{{ $student['name'] }}</div>
                    <div>Ngày Sinh: <strong>{{ $student['birthday'] }}</strong></div>
                    <div>Lớp: <strong>{{ $student['level'] }}</strong></div>
                    <div>Giảng Viên: <strong>{{ $student['teacher_name'] }}</strong></div>
                    <div>Ca: <strong>{{ $student['shift'] }}</strong></div>
                    <div>Giờ: <strong>{{ $student['time'] }}</strong></div>
                </div>
            </div>
        </div>
        <div class="footer">
            Địa chỉ: 123 Đường ABC, Quận X, TP.HCM | SĐT: 0123.456.789
        </div>

    </div>
    <div class="card-container back page-break">
        {{-- <img src="{{ public_path('images/logo.png') }}" class="watermark"> --}}

        <div class="qr-section">
            <img src="data:image/svg+xml;base64, {!! $qrCode !!}"
                style="width: 2.5cm; background: white; padding: 2px;">
            {{-- <p style="font-size: 9px; margin: 2px; font-weight: bold;">{{ $student['uuid'] }}</p> --}}
        </div>

        <div class="notes">
            <strong>LƯU Ý:</strong><br>
            - Thẻ dùng để điểm danh khi vào lớp.<br>
            - Không cho mượn thẻ dưới mọi hình thức.<br>
            - Nếu mất thẻ vui lòng liên hệ văn phòng để cấp lại.
        </div>
    </div>
</body>

</html>
