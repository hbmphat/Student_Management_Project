<?php

namespace App\Http\Controllers;

use App\Models\RegistrationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RegistrationCodeController extends Controller
{
    //
    private function checkAdmin(){
        if(Auth::user()->role !== 'admin'){
            abort(403, 'Truy cập bị từ chối. Chỉ admin mới có quyền thực hiện hành động này.');
        }
    }
    public function index()
    {
        $this->checkAdmin();
        // Lấy danh sách mã kèm theo thông tin người tạo và người dùng
        $codes = RegistrationCode::with(['creator', 'user'])->orderBy('created_at', 'desc')->get();
        return view('registration_codes.index', compact('codes'));
    }

    // Nút bấm sinh mã tự động (AJAX)
    public function store(Request $request)
    {
        $this->checkAdmin();

        // 1. Bắt buộc phải có mật khẩu gửi lên
        $request->validate([
            'password' => 'required|string'
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu xác nhận.'
        ]);

        // 2. Kiểm tra mật khẩu Admin nhập vào có đúng không
        if (!\Illuminate\Support\Facades\Hash::check($request->password, Auth::user()->password)) {
            // Trả về lỗi 422 (Unprocessable Entity) để AJAX tự động in ra chữ đỏ
            return response()->json(['errors' => ['password' => ['Mật khẩu xác nhận không chính xác!']]], 422);
        }
        
        // 3. Tạo mã ngẫu nhiên: NV- + 6 số (VD: NV-482910)
        $codeString = 'NV-' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        RegistrationCode::create([
            'code' => $codeString,
            'created_by' => Auth::id(),
            'is_used' => false,
        ]);

        return response()->json([
            'success' => 'Tạo mã thành công!',
            'code' => $codeString
        ]);
    }

    // Xóa mã (AJAX)
    public function destroy($id)
    {
        $this->checkAdmin();
        $code = RegistrationCode::findOrFail($id);

        if ($code->is_used) {
            return response()->json(['error' => 'Không thể xóa mã đã được sử dụng!'], 400);
        }

        $code->delete();
        return response()->json(['success' => 'Đã xóa mã đăng ký.']);
    }
}
