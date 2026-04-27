<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $courses = Course::orderBy('id', 'asc')->get();
        return view('courses.index', compact('courses'));
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
            'name' => 'required|string|max:255|unique:courses,name',
            'duration_months' => 'required|integer|min:1',
            'weekly_price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên khóa học.',
            'name.unique' => 'Tên khóa học đã tồn tại.',
            'duration_months.required' => 'Vui lòng nhập số tháng.',
            'weekly_price.required' => 'Vui lòng nhập giá mỗi tuần.',
            'description.string' => 'Mô tả phải là một chuỗi ký tự.'

        ]);

        Course::create($request->all());
        
        return response()->json(['success' => 'Thêm khóa học thành công!']);
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
    public function edit(Course $course)
    {
        //
        return response()->json($course);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255|unique:courses,name,' . $course->id,
            'duration_months' => 'required|integer|min:1',
            'weekly_price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ], [
            'name.required' => 'Vui lòng nhập tên khóa học.',
            'name.unique' => 'Tên khóa học đã bị trùng.',
            'duration_months.required' => 'Vui lòng nhập số tháng.',
            'weekly_price.required' => 'Vui lòng nhập giá mỗi tuần.',
            'description.string' => 'Mô tả phải là một chuỗi ký tự.'
        ]);

        $course->update($request->all());

        return response()->json(['success' => 'Cập nhật khóa học thành công!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
        $course->delete();
        return response()->json(['success' => 'Xóa khóa học thành công!']);
    }
    public function exportPdf()
    {
        $courses = Course::all();

        $data = [
            'center_name' => 'TRUNG TÂM NGOẠI NGỮ ENGBREAK',
            'slogan' => 'Bứt phá giới hạn ngôn ngữ của bạn!',
            'hotline' => '1900 8888',
            'address' => 'Tòa nhà EngBreak, 123 Đường ABC, Quận X, TP.HCM',
            'courses' => $courses,
            'date' => date('d/m/Y')
        ];

        $pdf = Pdf::loadView('courses.pdf_brochure', $data);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('EngBreak_BaoGiaKhoaHoc_' . date('dmY') . '.pdf');
    }
}
