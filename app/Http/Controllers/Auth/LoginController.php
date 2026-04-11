<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
    protected $redirectTo = '/teachers';

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

    // 2. Ghi đè: Hành động ngay sau khi đăng nhập thành công
    protected function authenticated(Request $request, $user)
    {
        // Kiểm tra xem user có bị khóa không?
        if ($user->status === 'locked') {
            Auth::logout(); // Đăng xuất ngay lập tức
            return redirect('/login')->withErrors([
                'username' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.',
            ]);
        }

        // Nếu bình thường thì cho qua
        return redirect()->intended($this->redirectPath());
    }
    protected function loggedOut(Request $request)
    {
        return redirect('/login');
    }
}
