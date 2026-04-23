@extends('layouts.master')
@section('title', 'Quản lý Học viên | ENGBREAK')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="fas fa-user-graduate me-2"></i> Danh Sách Học viên</h2>
        <button class="btn btn-primary fw-bold shadow-sm" onclick="openAddStudentModal()">
            <i class="fas fa-plus-circle"></i> Thêm Học viên
        </button>
    </div>

    {{-- <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-student" class="form-control border-start-0" placeholder="Tìm kiếm theo Tên, Mã HV hoặc SĐT phụ huynh...">
            </div>
        </div>
    </div> --}}

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Học viên</th>
                            <th>Ngày sinh</th>
                            <th>SĐT Phụ huynh</th>
                            <th>Email Phụ huynh</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body">
                        @forelse($students as $student)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $student->display_avatar }}" class="rounded-circle me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $student->name }}</div>
                                        <div class="text-muted small"><i class="fas fa-id-badge"></i> {{ $student->uuid }} | {{ $student->gender == 'male' ? 'Nam' : ($student->gender == 'female' ? 'Nữ' : 'Khác') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($student->dob)->format('d/m/Y') }}</td>
                            <td><div class="fw-bold text-primary">{{ $student->parent_phone }}</div></td>
                            <td><div class="fw-bold text-primary">{{ $student->parent_email }}</div></td>
                            <td class="text-center">
                                @if($student->status == 'studying') <span class="badge bg-success">Đang học</span>
                                @elseif($student->status == 'dropped') <span class="badge bg-danger">Nghỉ học</span>
                                @else <span class="badge bg-warning text-dark">Bảo lưu</span> @endif
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary fw-bold" onclick="viewStudent({{ $student->id }})">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Chưa có học viên nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('students._modal_add')
@include('students._modal_detail')
@include('students._scripts')

@endsection