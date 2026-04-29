<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\RegistrationCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'registration_code' => 'required|string',
            'old_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.'
        ]);

        if (!Auth::check()) {
            return response()->json(['errors' => ['auth' => ['Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.']]], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // 1. Xác thực Mã đăng ký nhân sự gắn với tài khoản này
        $isValidCode = RegistrationCode::where('used_by', $user->id)
            ->where('code', $request->registration_code)
            ->exists();

        if (!$isValidCode) {
            return response()->json(['errors' => ['registration_code' => ['Mã nhân sự không khớp với thông tin đăng ký của bạn.']]], 422);
        }

        // 2. Xác thực Mật khẩu cũ
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['errors' => ['old_password' => ['Mật khẩu hiện tại không chính xác.']]], 422);
        }

        // 3. Cập nhật mật khẩu mới
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Đã đổi mật khẩu thành công!']);
    }
}
