<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Giảng viên</title>
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
    </div>

    @foreach($teachers as $teacher)
    <div class="course-box">
        <h3 class="course-title">📚 {{ $teacher->name }} (Mã GV: {{ $teacher->teacher_code }})</h3>
        <p style="font-size: 13px; margin-bottom: 5px;"><i>Số điện thoại: {{ $teacher->phone }}</i></p>
        <p style="font-size: 13px; margin-bottom: 5px;"><i>Email: {{ $teacher->email }}</i></p>
        
        <table class="pricing">
            <thead>
                <tr>
                    <th>Tên Giảng viên</th>
                    <th>Ngày sinh</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Trạng thái</th>
                    <th>Bằng cấp</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher->date_of_birth ? \Carbon\Carbon::parse($teacher->date_of_birth)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $teacher->phone }}</td>
                    <td>{{ $teacher->email ?? 'N/A' }}</td>
                    <td>
                        @if($teacher->status == 'available')
                            Đang trống
                        @elseif($teacher->status == 'teaching')
                            Đang có lớp
                        @else
                            Ngừng dạy
                        @endif
                    </td>
                    <td>{{ $teacher->qualifications ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="footer">
        <p>Danh sách giảng viên được xuất vào ngày {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>

</body>
</html>