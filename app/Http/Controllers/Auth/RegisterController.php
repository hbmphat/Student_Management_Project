<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'], // Ràng buộc username
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'registration_code' => [
                'required',
                'string',
                // Viết Custom Rule để kiểm tra Mã Đăng Ký
                function ($attribute, $value, $fail) {
                    $code = \App\Models\RegistrationCode::where('code', $value)
                        ->where('is_used', false) // Mã phải chưa được dùng
                        ->first();
                    if (!$code) {
                        $fail('Mã đăng ký không tồn tại hoặc đã được sử dụng.');
                    }
                },
            ],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'registration_code.required' => 'Vui lòng nhập mã đăng ký.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        // Tạo tài khoản
        $user = \App\Models\User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role' => 'staff', // Mặc định ai đăng ký cũng là nhân viên
            'status' => 'active',
        ]);

        // Đánh dấu Mã Đăng Ký đã được sử dụng
        $code = \App\Models\RegistrationCode::where('code', $data['registration_code'])->first();
        $code->update([
            'is_used' => true,
            'used_by' => $user->id,
        ]);

        return $user;
    }
    protected function registered(Request $request, $user)
    {
        // Gửi toast success và redirect
        return redirect($this->redirectPath())
                    ->with('success', 'Chúc mừng! Tài khoản của bạn đã được tạo thành công. Đăng nhập để tiếp tục.');
    }
}
