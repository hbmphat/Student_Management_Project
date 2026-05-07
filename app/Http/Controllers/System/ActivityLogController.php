<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = \Spatie\Activitylog\Models\Activity::with(['causer', 'subject'])
                ->latest()
                ->limit(50)
                ->get()
                ->map(function ($log) {
                    // 1. Ánh xạ tên Model sang Tiếng Việt
                    $tableNames = [
                        'Student' => 'Học viên',
                        'User' => 'Nhân viên/GV',
                        'Payment' => 'Thanh toán',
                        'ClassRoom' => 'Lớp học',
                        'Tuition' => 'Học phí',
                        'Promotion' => 'Khuyến mãi',
                        'Teacher' => 'Giảng viên',
                        'Shift' => 'Ca học',
                        'Course' => 'Khóa học',
                    ];

                    $modelName = class_basename($log->subject_type);
                    $translatedTable = $tableNames[$modelName] ?? ($log->log_name === 'auth' ? 'Hệ thống' : $modelName);

                    // 2. Lấy tên đối tượng (như đã làm ở bước trước)
                    $subjectName = "ID: #{$log->subject_id}";
                    if ($log->subject) {
                        $subjectName = $log->subject->name ?? ($log->subject->receipt_code ?? $subjectName);
                    }

                    return [
                        'time' => $log->created_at->format('H:i d/m/Y'),
                        'causer' => $log->causer->name ?? 'Hệ thống',
                        'event' => $log->event,
                        'table_name' => $translatedTable, // Cột mới bổ sung
                        'subject' => $subjectName,
                        'properties' => $log->properties,
                    ];
                });
            return response()->json($logs);
        }
    }
}
