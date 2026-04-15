<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Container\Attributes\Tag;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $teachers = Teacher::orderBy('created_at', 'desc')->get();
        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'teacher_code' => 'required|string|max:50|unique:teachers,teacher_code',
            'name'         => 'required|string|max:255',
            'gender'       => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'phone'        => 'required|string|max:20|unique:teachers,phone',
            'email'        => 'nullable|email|unique:teachers,email',
            'status'       => 'required|in:available,teaching,inactive'
        ], [
            'teacher_code.required' => 'Vui lòng nhập Mã giảng viên.',
            'teacher_code.unique'   => 'Mã giảng viên này đã tồn tại.',
            'name.required'         => 'Vui lòng nhập Tên giảng viên.',
            'gender.required'       => 'Vui lòng chọn Giới tính.',
            'date_of_birth.required' => 'Vui lòng nhập Ngày sinh.',
            'phone.required'        => 'Vui lòng nhập Số điện thoại.',
            'phone.unique'          => 'Số điện thoại này đã được sử dụng.',
            'email.email'           => 'Định dạng email không hợp lệ.',
            'email.unique'          => 'Email này đã được đăng ký cho GV khác.'
        ]);

        Teacher::create($request->all());

        return response()->json(['success' => 'Thêm Giảng viên thành công!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
        return response()->json($teacher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
        // Chú ý đoạn nối chuỗi id ở cuối để bỏ qua lỗi trùng với chính nó
        $request->validate([
            'teacher_code' => 'required|string|max:50|unique:teachers,teacher_code,' . $teacher->id,
            'name'         => 'required|string|max:255',
            'gender'       => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'phone'        => 'required|string|max:20|unique:teachers,phone,' . $teacher->id,
            'email'        => 'nullable|email|unique:teachers,email,' . $teacher->id,
            'status'       => 'required|in:available,teaching,inactive'
        ], [
            'teacher_code.required' => 'Vui lòng nhập Mã giảng viên.',
            'teacher_code.unique'   => 'Mã giảng viên này đã bị trùng.',
            'name.required'         => 'Vui lòng nhập Tên giảng viên.',
            'gender.required'       => 'Vui lòng chọn Giới tính.',
            'date_of_birth.required' => 'Vui lòng nhập Ngày sinh.',
            'phone.required'        => 'Vui lòng nhập Số điện thoại.',
            'phone.unique'          => 'Số điện thoại này đã bị trùng.',
            'email.email'           => 'Định dạng email không hợp lệ.',
            'email.unique'          => 'Email này đã bị trùng.'
        ]);

        $teacher->update($request->all());

        return response()->json(['success' => 'Cập nhật Giảng viên thành công!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
        $teacher->delete();
        return response()->json(['success' => 'Xóa giảng viên thành công!']);
    }
}
