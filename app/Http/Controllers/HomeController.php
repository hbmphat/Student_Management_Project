<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\RegistrationCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Payment;
use App\Models\Tuition;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        // 1. TÍNH TOÁN 4 CHỈ SỐ KPI CHÍNH
        $totalStudents = Student::where('status', 'studying')->count();
        $totalClasses = ClassRoom::where('status', 'active')->count(); // Thay đổi trạng thái tùy theo DB của bạn
        $totalTeachers = Teacher::count(); // Sử dụng model Teacher nếu có
        
        // Doanh thu tháng này
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $revenueThisMonth = Payment::whereMonth('created_at', $currentMonth)
                                   ->whereYear('created_at', $currentYear)
                                   ->sum('final_amount');

        // 2. BIỂU ĐỒ TRẠNG THÁI HỌC VIÊN
        $studentStatuses = Student::select('status', DB::raw('count(*) as total'))
                                  ->groupBy('status')
                                  ->pluck('total', 'status')->toArray();
        
        $chartStudents = [
            'studying' => $studentStatuses['studying'] ?? 0,
            'reserved' => $studentStatuses['reserved'] ?? 0,
            'dropped' => $studentStatuses['dropped'] ?? 0,
        ];

        // 3. BIỂU ĐỒ DOANH THU 6 THÁNG QUA
        $revenueData = [];
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenueLabels[] = 'Tháng ' . $month->format('m/Y');
            
            $sum = Payment::whereMonth('created_at', $month->month)
                          ->whereYear('created_at', $month->year)
                          ->sum('final_amount');
            $revenueData[] = $sum;
        }

        // 4. CẢNH BÁO: HỌC PHÍ SẮP HẾT HẠN (Trong 7 ngày tới)
        $today = Carbon::today();
        $next7Days = Carbon::today()->addDays(7);
        $expiringTuitions = Tuition::with(['student', 'classRoom'])
            ->whereNotNull('to_date')
            ->whereBetween('to_date', [$today, $next7Days])
            ->orderBy('to_date', 'asc')
            ->limit(5)
            ->get();

        // 5. GIAO DỊCH MỚI NHẤT
        $recentPayments = Payment::with(['student', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('home', compact(
            'totalStudents', 'totalClasses', 'totalTeachers', 'revenueThisMonth',
            'chartStudents', 'revenueLabels', 'revenueData',
            'expiringTuitions', 'recentPayments'
        ));
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
