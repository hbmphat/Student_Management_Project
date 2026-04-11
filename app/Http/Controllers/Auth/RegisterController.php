<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

        // Đánh dấu Mã Đăng Ký đã bị xài
        $code = \App\Models\RegistrationCode::where('code', $data['registration_code'])->first();
        $code->update([
            'is_used' => true,
            'used_by' => $user->id,
        ]);

        return $user;
    }
}
