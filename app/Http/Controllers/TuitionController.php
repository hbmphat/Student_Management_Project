<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Promotion;
use App\Models\Tuition;
use App\Models\TuitionHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TuitionController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        // Sắp xếp theo ưu tiên: chưa đóng -> quá hạn -> hết -> còn học phí
        $query = Tuition::with(['student', 'classRoom.course'])
            ->orderByRaw(
                "CASE
                    WHEN to_date IS NULL THEN 0
                    WHEN to_date < ? THEN 1
                    WHEN DATE(to_date) = ? THEN 2
                    ELSE 3
                END",
                [$today, $today]
            )
            ->orderByRaw('COALESCE(to_date, created_at) ASC')
            ->orderByDesc('id');

        $tuitions = $query->paginate(15);

        // Xử lý 4 trạng thái realtime
        $todayDate = Carbon::today();

        foreach ($tuitions as $t) {
            if (!$t->to_date) {
                $t->status_color = 'secondary';
                $t->status_text = 'Chưa đóng học phí';
            } else {
                $toDate = Carbon::parse($t->to_date)->startOfDay();

                if ($toDate->lt($todayDate)) {
                    $t->status_color = 'danger';
                    $t->status_text = 'Quá hạn học phí';
                } elseif ($toDate->equalTo($todayDate)) {
                    $t->status_color = 'warning';
                    $t->status_text = 'Hết học phí';
                } else {
                    $t->status_color = 'success';
                    $t->status_text = 'Còn học phí';
                }
            }
        }

        return view('tuitions.index', compact('tuitions'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'tuition_id' => 'required|exists:tuitions,id',
            'paid_weeks' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,vietqr',
            'promotion_id' => 'nullable|exists:promotions,id',
        ]);

        $result = DB::transaction(function () use ($request) {
            $tuition = Tuition::with('classRoom.course')->lockForUpdate()->findOrFail($request->tuition_id);

            $pricePerWeek = (float) ($tuition->classRoom->course->weekly_price ?? 0);
            $originalAmount = round($request->paid_weeks * $pricePerWeek, 2);

            $discountAmount = 0;
            $promo = null;

            if ($request->filled('promotion_id')) {
                $promo = Promotion::whereKey($request->promotion_id)
                    ->where('is_active', true)
                    ->first();

                if (!$promo) {
                    throw ValidationException::withMessages([
                        'promotion_id' => 'Khuyến mãi không hợp lệ hoặc đã bị tắt.',
                    ]);
                }

                $discountAmount = round($originalAmount * ($promo->discount_percent / 100), 2);
            }

            $finalAmount = max(round($originalAmount - $discountAmount, 2), 0);

            do {
                $receiptCode = 'RC' . now()->format('ymd') . strtoupper(Str::random(4));
            } while (Payment::where('receipt_code', $receiptCode)->exists());

            Payment::create([
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

            return [
                'receipt_code' => $receiptCode,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công! Đã tạo biên lai: ' . $result['receipt_code'],
            'receipt_code' => $result['receipt_code'],
        ]);
    }

    public function processExtend(Request $request)
    {
        $request->validate([
            'tuition_id' => 'required|exists:tuitions,id',
            'receipt_code' => 'required|string|max:50',
        ]);

        $result = DB::transaction(function () use ($request) {
            $tuition = Tuition::lockForUpdate()->findOrFail($request->tuition_id);
            $payment = Payment::where('receipt_code', trim($request->receipt_code))->lockForUpdate()->first();

            if (!$payment) {
                throw ValidationException::withMessages([
                    'receipt_code' => 'Mã biên lai không tồn tại.',
                ]);
            }

            if ($payment->is_used) {
                throw ValidationException::withMessages([
                    'receipt_code' => 'Mã biên lai này đã được dùng để gia hạn.',
                ]);
            }

            if ($payment->student_id !== $tuition->student_id || $payment->class_room_id !== $tuition->class_room_id) {
                throw ValidationException::withMessages([
                    'receipt_code' => 'Mã biên lai không khớp với học viên hoặc lớp học hiện tại.',
                ]);
            }

            $daysAdded = (int) $payment->paid_weeks * 7;
            $today = Carbon::today()->startOfDay();
            $oldToDate = $tuition->to_date ? Carbon::parse($tuition->to_date)->startOfDay() : null;
            $baseDate = $oldToDate && $oldToDate->greaterThan($today) ? $oldToDate->copy() : $today->copy();
            $newToDate = $baseDate->addDays($daysAdded);

            $fromDate = $tuition->from_date ? Carbon::parse($tuition->from_date)->startOfDay() : $today->copy();
            if (!$oldToDate || $oldToDate->lessThan($today)) {
                $fromDate = $today->copy();
            }

            $tuition->update([
                'from_date' => $fromDate->toDateString(),
                'to_date' => $newToDate->toDateString(),
            ]);

            $payment->update([
                'is_used' => true,
            ]);

            TuitionHistory::create([
                'tuition_id' => $tuition->id,
                'payment_id' => $payment->id,
                'action_type' => 'extend',
                'days_added' => $daysAdded,
                'old_to_date' => $oldToDate?->toDateString(),
                'new_to_date' => $newToDate->toDateString(),
                'note' => 'Gia hạn bằng biên lai ' . $payment->receipt_code . ' (' . $payment->paid_weeks . ' tuần)',
                'created_by' => Auth::id(),
            ]);

            return [
                'receipt_code' => $payment->receipt_code,
                'new_to_date' => $newToDate->toDateString(),
                'paid_weeks' => $payment->paid_weeks,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Gia hạn thành công từ biên lai ' . $result['receipt_code'] . '.',
            'receipt_code' => $result['receipt_code'],
            'new_to_date' => $result['new_to_date'],
            'paid_weeks' => $result['paid_weeks'],
        ]);
    }

    public function history($tuitionId)
    {
        $tuition = Tuition::with(['student', 'classRoom.course', 'histories.payment.promotion', 'histories.creator'])
            ->findOrFail($tuitionId);

        $payments = Payment::with(['promotion', 'creator'])
            ->where('student_id', $tuition->student_id)
            ->where('class_room_id', $tuition->class_room_id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Payment $payment) {
                return [
                    'receipt_code' => $payment->receipt_code,
                    'paid_weeks' => $payment->paid_weeks,
                    'original_amount' => $payment->original_amount,
                    'discount_amount' => $payment->discount_amount,
                    'final_amount' => $payment->final_amount,
                    'payment_method' => $payment->payment_method,
                    'payment_method_label' => $payment->payment_method === 'cash' ? 'Tiền mặt' : 'VietQR',
                    'is_used' => (bool) $payment->is_used,
                    'promotion_name' => $payment->promotion->name ?? null,
                    'created_at' => optional($payment->created_at)->format('d/m/Y H:i'),
                ];
            });

        $tuitionHistories = $tuition->histories
            ->sortByDesc('created_at')
            ->values()
            ->map(function (TuitionHistory $history) {
                return [
                    'action_type' => $history->action_type,
                    'action_label' => match ($history->action_type) {
                        'extend' => 'Gia hạn',
                        'compensate' => 'Bù hạn',
                        'manual_edit' => 'Chỉnh tay',
                        default => ucfirst($history->action_type),
                    },
                    'days_added' => $history->days_added,
                    'old_to_date' => $history->old_to_date,
                    'new_to_date' => $history->new_to_date,
                    'note' => $history->note,
                    'receipt_code' => $history->payment?->receipt_code,
                    'created_at' => optional($history->created_at)->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'tuition' => [
                'id' => $tuition->id,
                'student_name' => $tuition->student->name ?? 'N/A',
                'student_uuid' => $tuition->student->uuid ?? 'N/A',
                'class_name' => $tuition->classRoom->name ?? 'N/A',
                'current_from_date' => $tuition->from_date,
                'current_to_date' => $tuition->to_date,
            ],
            'payments' => $payments,
            'histories' => $tuitionHistories,
        ]);
    }

    // 2. Hàm Xuất PDF Biên lai
    public function printReceipt($receiptCode)
    {
        $payment = Payment::with(['student', 'classRoom.course', 'creator', 'promotion'])
            ->where('receipt_code', $receiptCode)
            ->firstOrFail();

        $pdf = Pdf::loadView('tuitions.pdf_receipt', compact('payment'));
        $pdf->setPaper('a5', 'landscape');

        return $pdf->stream('Bien-Lai-' . $payment->receipt_code . '.pdf');
    }
}
