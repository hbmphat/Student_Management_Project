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
    // Trong RegistrationCodeController.php

public function index(Request $request)
{
    if ($request->ajax()) {
        // Eager load quan hệ 'user' (giả định bạn đã đặt tên quan hệ trong Model là usedBy hoặc user)
        $codes = RegistrationCode::with('user:id,name,username')->orderBy('id', 'desc')->get();
        return response()->json($codes);
    }
    return view('registration_codes.index');
}

public function toggleBlock($id)
{
    $code = RegistrationCode::findOrFail($id);
    
    // Đảo trạng thái khóa của mã
    $code->is_blocked = !$code->is_blocked;
    $code->save();

    // Nếu mã này đã được sử dụng, ta khóa/mở khóa luôn tài khoản liên kết
    if ($code->is_used && $code->used_by) {
        $user = \App\Models\User::find($code->used_by);
        if ($user) {
            $user->status = $code->is_blocked ? 'locked' : 'active';
            $user->save();
        }
    }

    $statusText = $code->is_blocked ? 'Đã khóa' : 'Đã mở khóa';
    return response()->json(['success' => true, 'message' => $statusText . ' thành công!']);
}

    public function store(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'password' => 'required|string'
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu xác nhận.'
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, Auth::user()->password)) {
            return response()->json(['errors' => ['password' => ['Mật khẩu xác nhận không chính xác!']]], 422);
        }
        
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
