<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Promotion;
use App\Models\TuitionHistory;
use Illuminate\Http\Request;
use App\Models\Tuition;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class TuitionController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách học phí, kèm theo thông tin Học viên và Lớp học
        $query = Tuition::with(['student', 'classRoom.course'])->latest();

        // (Tùy chọn) Thêm logic tìm kiếm ở đây nếu cần

        $tuitions = $query->paginate(15);

        // Xử lý trạng thái Xanh/Vàng/Đỏ realtime
        $today = Carbon::today();

        foreach ($tuitions as $t) {
            if (!$t->to_date) {
                $t->status_color = 'secondary';
                $t->status_text = 'Chưa đóng phí';
            } else {
                $toDate = Carbon::parse($t->to_date)->startOfDay();

                if ($toDate->isPast() && !$toDate->isToday()) {
                    $t->status_color = 'danger'; // Đỏ: Quá hạn
                    $t->status_text = 'Quá hạn';
                } elseif ($toDate->diffInDays($today) <= 3) {
                    // Vàng: Còn <= 3 ngày là hết hạn (Sắp hết trong ngày/tuần)
                    $t->status_color = 'warning';
                    $t->status_text = 'Sắp hết hạn';
                } else {
                    $t->status_color = 'success'; // Xanh: Còn hạn
                    $t->status_text = 'Còn hạn';
                }
            }
        }

        return view('tuitions.index', compact('tuitions'));
    }
    public function processPayment(Request $request)
    {
        $request->validate([
            'tuition_id' => 'required|exists:tuitions,id',
            'paid_weeks' => 'required|integer|min:1', // Validate số tuần
            'payment_method' => 'required|in:cash,vietqr',
            'promotion_id' => 'nullable|exists:promotions,id'
        ]);

        $result = DB::transaction(function () use ($request) {
            // 1. Lấy thông tin Tuition -> ClassRoom -> Course
            $tuition = Tuition::with('classRoom.course')->lockForUpdate()->findOrFail($request->tuition_id);

            // 2. Tính toán Giá gốc = Số tuần x Giá 1 tuần của Khóa học
            $pricePerWeek = (float) ($tuition->classRoom->course->weekly_price ?? 0);
            $originalAmount = round($request->paid_weeks * $pricePerWeek, 2);

            $discountAmount = 0;
            $promo = null;

            // 3. Nếu có khuyến mãi
            if ($request->filled('promotion_id')) {
                $promo = Promotion::whereKey($request->promotion_id)
                    ->where('is_active', true)
                    ->first();

                if (!$promo) {
                    throw ValidationException::withMessages([
                        'promotion_id' => 'Khuyến mãi không hợp lệ hoặc đã bị tắt.',
                    ]);
                }

                if ($promo) {
                    $discountAmount = round($originalAmount * ($promo->discount_percent / 100), 2);
                }
            }

            $finalAmount = max(round($originalAmount - $discountAmount, 2), 0);

            // 4. Sinh Mã Biên Lai duy nhất
            do {
                $receiptCode = 'RC' . now()->format('ymd') . strtoupper(Str::random(4));
            } while (Payment::where('receipt_code', $receiptCode)->exists());

            // 5. Lưu payment
            $payment = Payment::create([
                'student_id' => $tuition->student_id,
                'class_room_id' => $tuition->class_room_id,
                'paid_weeks' => $request->paid_weeks,
                'promotion_id' => $promo?->id,
                'receipt_code' => $receiptCode,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'payment_method' => $request->payment_method,
                'is_used' => false,
                'created_by' => Auth::id(),
            ]);



            // 6. Cập nhật hạn học phí và ghi lịch sử
            $daysAdded = (int) $request->paid_weeks * 7;
            $oldToDate = $tuition->to_date ? Carbon::parse($tuition->to_date)->startOfDay() : null;
            $baseDate = $oldToDate ? $oldToDate->copy() : Carbon::today();
            $newToDate = $baseDate->addDays($daysAdded);

            $fromDate = $tuition->from_date
                ? Carbon::parse($tuition->from_date)->startOfDay()
                : Carbon::today();

            $tuition->update([
                'from_date' => $fromDate->toDateString(),
                'to_date' => $newToDate->toDateString(),
            ]);

            TuitionHistory::create([
                'tuition_id' => $tuition->id,
                'payment_id' => $payment->id,
                'action_type' => 'extend',
                'days_added' => $daysAdded,
                'old_to_date' => $oldToDate?->toDateString(),
                'new_to_date' => $newToDate->toDateString(),
                'note' => 'Thanh toán ' . $request->paid_weeks . ' tuần' . ($promo ? ' với khuyến mãi ' . $promo->name : ''),
                'created_by' => Auth::id(),
            ]);

            return [
                'receipt_code' => $receiptCode,
                'new_to_date' => $newToDate->toDateString(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công! Mã biên lai: ' . $result['receipt_code'],
            'receipt_code' => $result['receipt_code'],
            'new_to_date' => $result['new_to_date'],
        ]);
    }
    // 2. Hàm Xuất PDF Biên lai
    public function printReceipt($receiptCode)
    {
        // Lấy thông tin hóa đơn kèm các bảng liên quan
        $payment = Payment::with(['student', 'classRoom.course', 'creator', 'promotion'])
            ->where('receipt_code', $receiptCode)
            ->firstOrFail();

        // Nạp View và truyền biến $payment vào
        $pdf = Pdf::loadView('tuitions.pdf_receipt', compact('payment'));

        // Thiết lập khổ giấy A5 ngang (thường dùng cho biên lai)
        $pdf->setPaper('a5', 'landscape');

        // Xuất trực tiếp trên trình duyệt (stream) hoặc dùng download() để ép tải về
        return $pdf->stream('Bien-Lai-' . $payment->receipt_code . '.pdf');
    }
}
