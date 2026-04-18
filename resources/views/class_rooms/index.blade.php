@extends('layouts.master')
@section('title', 'Quản lý Lớp học | ENGBREAK')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary"><i class="fas fa-chalkboard-teacher me-2"></i> Quản lý Lớp học</h2>
            <div>
                <button class="btn btn-outline-secondary me-2" onclick="$('#shiftsModal').modal('show')">
                    <i class="fas fa-clock"></i> Quản lý Ca học
                </button>
                <button class="btn btn-primary" onclick="openAddClassModal()">
                    <i class="fas fa-plus-circle"></i> Thêm Lớp Mới
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="class-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tên Lớp học</th>
                                <th>Ca học / Giờ học</th>
                                <th>Giảng viên</th>
                                <th class="text-center">Sĩ số</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classRooms as $class)
                                <tr id="row-{{ $class->id }}">
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary">{{ $class->name }}</div>
                                        <div class="text-muted small">Phòng: {{ $class->room ?? 'Chưa xếp' }}</div>
                                    </td>
                                    <td>
                                        <div><span class="badge bg-info text-dark">{{ $class->shift->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-muted small mt-1">
                                            {{ \Carbon\Carbon::parse($class->shift->start_time ?? now())->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($class->shift->end_time ?? now())->format('H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $class->teacher->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-secondary rounded-pill">0</span></td>
                                    <td class="text-center">
                                        @if ($class->status == 'active')
                                            <span class="badge bg-success">Đang học</span>
                                        @elseif($class->status == 'pending')
                                            <span class="badge bg-warning text-dark">Sắp mở</span>
                                        @elseif($class->status == 'completed')
                                            <span class="badge bg-primary">Kết thúc</span>
                                        @else
                                            <span class="badge bg-danger">Đã Hủy</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary fw-bold"
                                            onclick="viewClass({{ $class->id }})">
                                            <i class="fas fa-eye"></i> Xem / Quản lý
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Chưa có lớp học nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('class_rooms._modal_add_class')
    @include('class_rooms._modal_class_detail')
    @include('class_rooms._modal_shift')
    @include('class_rooms._scripts')

@endsection
