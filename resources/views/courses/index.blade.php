@extends('layouts.master')

@section('title', 'Quản lý Khóa học | ENGBREAK')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 text-primary fw-bold">Danh sách Khóa học</h2>
        <button class="btn btn-success" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Thêm Khóa học mới
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            {{-- <th>ID</th> --}}
                            <th>Tên khóa học</th>
                            <th>Thời lượng</th>
                            <th>Giá / Tuần</th>
                            <th>Mô tả</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr id="row-{{ $course->id }}">
                                {{-- <td>#{{ $course->id }}</td> --}}
                                <td class="fw-bold">{{ $course->name }}</td>
                                <td>{{ $course->duration_months }} tháng</td>
                                <td class="text-danger fw-bold">{{ number_format($course->weekly_price, 0, ',', '.') }} đ</td>
                                <td>{{ $course->description }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary mx-1" onclick="openEditModal({{ $course->id }})">Sửa</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCourse({{ $course->id }})">Xóa</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu khóa học nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('courses._modal')
@include('courses._scripts')

@endsection



