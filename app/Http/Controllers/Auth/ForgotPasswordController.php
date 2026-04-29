<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RegistrationCode;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'registration_code' => 'required|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'registration_code.required' => 'Vui lòng nhập mã nhân viên.',
        ]);

        // Tìm mã đăng ký khớp với đúng user
        $registrationCode = RegistrationCode::where('code', $request->registration_code)
            ->whereHas('user', function ($q) use ($request) {
                $q->where('username', $request->username);
            })
            ->first();

        if (!$registrationCode || !$registrationCode->user) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Tên đăng nhập hoặc mã nhân viên không chính xác.');
        }

        $user = $registrationCode->user;

        // Cấp mật khẩu mới ngẫu nhiên
        $newPassword = Str::random(12);
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Gửi Email thông báo mật khẩu mới
        try {
            Mail::send('emails.new_password', ['name' => $user->name, 'password' => $newPassword], function ($message) use ($user) {
                $message->to($user->email)->subject('🔐 Mật khẩu mới cho tài khoản ENGBREAK của bạn');
            });

            return back()->with('status', 'Đã xác thực. Mật khẩu mới đã được gửi đến email: ' . $user->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể gửi email xác nhận. Vui lòng thử lại sau.');
        }
    }
}
