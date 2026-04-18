@extends('layouts.master')

@section('title', 'Quản lý Giảng viên | ENGBREAK')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 text-primary fw-bold">Danh sách Giảng viên</h2>
        <div>
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Thêm Giảng viên mới
        </button>
        <a href="{{ route('teachers.export-pdf') }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Xuất danh sách Giảng viên
        </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã GV</th>
                            <th>Tên Giảng viên</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr id="row-{{ $teacher->id }}">
                                <td class="fw-bold text-primary">{{ $teacher->teacher_code }}</td>
                                <td class="fw-bold">{{ $teacher->name }}</td>
                                <td>{{ $teacher->phone }}</td>
                                <td>{{ $teacher->email ?? '<span class="text-muted fst-italic">Chưa cập nhật</span>' }}</td>
                                <td>
                                    @if($teacher->status == 'available')
                                        <span class="badge bg-success">Đang trống</span>
                                    @elseif($teacher->status == 'teaching')
                                        <span class="badge bg-warning text-dark">Đang có lớp</span>
                                    @else
                                        <span class="badge bg-secondary">Ngừng dạy</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary mx-1" onclick="openEditModal({{ $teacher->id }})">Sửa</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTeacher({{ $teacher->id }})">Xóa</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu giảng viên nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('teachers._modal')
@include('teachers._scripts')

@endsection


