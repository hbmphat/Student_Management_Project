<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    // 1. Ghi đè: Ép Laravel dùng 'username' để đăng nhập thay vì 'email'
    public function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request, $user)
    {
        // Kiểm tra xem user có bị khóa không?
        if ($user->status === 'locked') {
            Auth::logout(); // Đăng xuất ngay lập tức
            // Dùng session('error') thay vì withErrors để Toast bắt được
            return redirect('/login')->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.');
        }

        // Chuyển hướng vào trong hệ thống kèm thông báo 'success'
        // File master.blade.php của bạn sẽ tự động bắt chữ 'success' này và hiện Toast!
        return redirect()->intended($this->redirectPath())
                         ->with('success', 'Chào mừng ' . $user->name . ' quay trở lại!');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'username' => ['Tên đăng nhập hoặc mật khẩu chưa đúng.'],
        ]);
    }

    protected function loggedOut(Request $request)
    {
        return redirect('/login');
    }
}
