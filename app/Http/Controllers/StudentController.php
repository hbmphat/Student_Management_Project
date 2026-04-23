<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Student::query();

        // Xử lý tìm kiếm realtime nếu có request AJAX
        if ($request->ajax()) {
            if ($request->has('search') && $request->search != '') {
                $q = $request->search;
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('uuid', 'LIKE', "%{$q}%")
                      ->orWhere('parent_phone', 'LIKE', "%{$q}%");
            }
            $students = $query->latest()->get();
            // Nạp thêm thuộc tính ảnh thông minh vào JSON
            $students->append('display_avatar'); 
            return response()->json(['students' => $students]);
        }

        // Load trang đầu tiên
        $students = $query->latest()->paginate(15);
        $provinces = \App\Models\Province::orderBy('name')->get();
        
        // Truyền thêm biến $provinces sang View
        return view('students.index', compact('students', 'provinces'));
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
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'parent_phone' => 'required|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Xử lý upload ảnh Avatar
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('students/avatars', 'public');
        }

        // UUID sẽ được tự động sinh ra nhờ hàm boot() trong Model
        $student = Student::create($data);

        return response()->json(['message' => "Đã thêm học viên: {$student->name} ({$student->uuid})"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $student = Student::with(['ward.district.province'])->findOrFail($id);
        $student->append('display_avatar'); // Trả về đường dẫn ảnh hợp lệ
        
        return response()->json(['student' => $student]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'parent_phone' => 'required|string|max:20',
            'status' => 'required|in:studying,dropped,reserved',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $student = Student::findOrFail($id);
        $data = $request->all();

        // Xử lý cập nhật ảnh (Xóa ảnh cũ, lưu ảnh mới)
        if ($request->hasFile('avatar')) {
            if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
                Storage::disk('public')->delete($student->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('students/avatars', 'public');
        }

        $student->update($data);

        return response()->json(['message' => 'Cập nhật thông tin học viên thành công!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $student = Student::findOrFail($id);
        $student->delete(); // Soft delete nhờ khai báo trong Model

        return response()->json(['message' => 'Đã chuyển học viên vào thùng rác.']);
    }

    // Nơi chứa giao diện In Thẻ (Sẽ làm ở Phase sau)
    public function printCard($id)
    {
        return app(StudentCardController::class)->generateCard($id);
    }
}
