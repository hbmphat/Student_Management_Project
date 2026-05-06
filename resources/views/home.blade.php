@extends('layouts.master')
@section('title', 'Dashboard | ENGBREAK')

@section('content')
<!-- Nhúng thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"><i class="fas fa-chart-line text-primary me-2"></i> Tổng quan Trung tâm</h2>
        <span class="text-muted"><i class="fas fa-clock"></i> Hôm nay: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
    </div>

    <!-- HÀNG 1: 4 THẺ CHỈ SỐ (KPI) -->
    <div class="row g-4 mb-4">
        <!-- Thẻ Doanh thu -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0d6efd, #0a58ca); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 text-white-50 fw-bold text-uppercase">Doanh thu tháng {{ \Carbon\Carbon::now()->month }}</p>
                            <h3 class="fw-bold mb-0">{{ number_format($revenueThisMonth) }} ₫</h3>
                        </div>
                        {{-- <div class="p-3 bg-white bg-opacity-25 rounded-3">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Thẻ Học viên -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 text-white-50 fw-bold text-uppercase">Học viên Đang học</p>
                            <h3 class="fw-bold mb-0">{{ $totalStudents }}</h3>
                        </div>
                        {{-- <div class="p-3 bg-white bg-opacity-25 rounded-3">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Thẻ Lớp học -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 text-dark-50 fw-bold text-uppercase">Lớp học Đang mở</p>
                            <h3 class="fw-bold mb-0">{{ $totalClasses }}</h3>
                        </div>
                        {{-- <div class="p-3 bg-dark bg-opacity-10 rounded-3">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Thẻ Giáo viên -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 text-white-50 fw-bold text-uppercase">Tổng Giảng viên</p>
                            <h3 class="fw-bold mb-0">{{ $totalTeachers }}</h3>
                        </div>
                        {{-- <div class="p-3 bg-white bg-opacity-25 rounded-3">
                            <i class="fas fa-chalkboard fa-2x"></i>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HÀNG 2: BIỂU ĐỒ -->
    <div class="row g-4 mb-4">
        <!-- Biểu đồ Doanh thu (Line Chart) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-chart-area text-primary me-2"></i> Tăng trưởng doanh thu (6 tháng)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Học viên (Doughnut Chart) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-chart-pie text-success me-2"></i> Trạng thái học viên</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="position: relative; height:250px; width:100%">
                        <canvas id="studentStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HÀNG 3: BẢNG DỮ LIỆU CẢNH BÁO & LỊCH SỬ -->
    <div class="row g-4">
        <!-- Giao dịch gần đây -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold"><i class="fas fa-history text-info me-2"></i> Giao dịch mới nhất</h5>
                    <a href="{{ route('tuitions.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Học viên</th>
                                    <th>Số tiền</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $pay)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        {{ $pay->student->name ?? 'N/A' }} <br>
                                        <small class="text-muted fw-normal"><i class="fas fa-receipt"></i> {{ $pay->receipt_code }}</small>
                                    </td>
                                    <td class="text-success fw-bold">+{{ number_format($pay->final_amount) }} ₫</td>
                                    <td class="text-muted small">{{ $pay->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-3 text-muted">Chưa có giao dịch nào.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cảnh báo Học phí -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-2">
                    <h5 class="fw-bold"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Sắp hết hạn học phí (7 ngày tới)</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($expiringTuitions as $tuition)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $tuition->student->name ?? 'N/A' }}</h6>
                                <small class="text-muted">Lớp: {{ $tuition->classRoom->name ?? 'N/A' }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark mb-1">Hạn: {{ \Carbon\Carbon::parse($tuition->to_date)->format('d/m/Y') }}</span>
                                <br>
                                <a href="{{ route('tuitions.index') }}" class="small text-decoration-none fw-bold text-primary">Gia hạn ngay <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-4 text-muted">Không có học viên nào sắp hết hạn học phí.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. BIỂU ĐỒ DOANH THU (LINE CHART) ---
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        
        // Tạo gradient màu cho Line chart
        let gradient = ctxRevenue.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.5)'); // Blue
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)'); // Transparent

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueLabels) !!},
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: {!! json_encode($revenueData) !!},
                    backgroundColor: gradient,
                    borderColor: '#0d6efd',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Làm cong đường line
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // --- 2. BIỂU ĐỒ TRẠNG THÁI HỌC VIÊN (DOUGHNUT CHART) ---
        const ctxStatus = document.getElementById('studentStatusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Đang học', 'Bảo lưu', 'Nghỉ học'],
                datasets: [{
                    data: [
                        {{ $chartStudents['studying'] }}, 
                        {{ $chartStudents['reserved'] }}, 
                        {{ $chartStudents['dropped'] }}
                    ],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Độ mỏng của vòng tròn
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection