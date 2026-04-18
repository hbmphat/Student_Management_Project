<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Shift;
use App\Models\ClassSchedule;
use App\Models\ClassSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClassRoomController extends Controller
{
    public function index()
    {
        // lấy danh sách lớp và dữ liệu liên quan
        $classRooms = ClassRoom::with(['course', 'teacher', 'shift'])->latest()->get();

        $courses = Course::all();
        $teachers = Teacher::whereIn('status', ['available', 'teaching'])->get();
        $shifts = Shift::all();

        return view('class_rooms.index', compact('classRooms', 'courses', 'teachers', 'shifts'));
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        // validate dữ liệu đầu vào
        $request->validate([
            'course_id' => 'required',
            'teacher_id' => 'required',
            'shift_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'days_of_week' => 'required|array|min:1', 
        ]);

        try {
            // bật transaction bảo vệ dữ liệu
            DB::beginTransaction();

            $course = Course::findOrFail($request->course_id);
            $teacher = Teacher::findOrFail($request->teacher_id);
            $shift = Shift::findOrFail($request->shift_id);

            // lấy chuỗi giờ học
            $timeString = \Carbon\Carbon::parse($shift->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($shift->end_time)->format('H:i');

            // xử lý mảng thứ trong tuần
            $dayNumbers = [];
            $hasSunday = false;
            foreach ($request->days_of_week as $day) {
                if ($day == 8) {
                    $hasSunday = true;
                } else {
                    $dayNumbers[] = $day;
                }
            }

            // format chuỗi thứ
            $daysString = count($dayNumbers) > 0 ? 'T' . implode(',', $dayNumbers) : '';
            if ($hasSunday) {
                $daysString = $daysString ? $daysString . ',CN' : 'CN';
            }

            // tạo tên lớp chuẩn
            $codeTeacher = $teacher->teacher_code;
            $newClassName = "{$course->name}-{$codeTeacher}-{$shift->name}({$daysString} {$timeString})";

            // lưu thông tin lớp
            $classRoom = ClassRoom::create([
                'name' => $newClassName,
                'course_id' => $request->course_id,
                'teacher_id' => $request->teacher_id,
                'shift_id' => $request->shift_id,
                'room' => $request->room,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'pending'
            ]);

            // lưu khuôn mẫu lịch học
            foreach ($request->days_of_week as $day) {
                ClassSchedule::create([
                    'class_room_id' => $classRoom->id,
                    'day_of_week' => $day
                ]);
            }

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            // tự động sinh các buổi học
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $currentDayVal = $date->dayOfWeekIso + 1;

                if (in_array($currentDayVal, $request->days_of_week)) {
                    ClassSession::create([
                        'class_room_id' => $classRoom->id,
                        'session_date' => $date->format('Y-m-d'),
                        'status' => 'scheduled'
                    ]);
                }
            }

            // xác nhận lưu
            DB::commit();

            return response()->json(['message' => "tạo thành công lớp: {$newClassName}"]);
        } catch (\Exception $e) {
            // hoàn tác nếu có lỗi
            DB::rollBack();
            return response()->json(['message' => 'lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        // lấy chi tiết lớp kèm lịch
        $classRoom = ClassRoom::with(['course', 'teacher', 'shift', 'schedules'])->findOrFail($id);

        // format ngày hiển thị text
        $classRoom->formatted_start_date = Carbon::parse($classRoom->start_date)->format('d/m/Y');
        $classRoom->formatted_end_date = Carbon::parse($classRoom->end_date)->format('d/m/Y');

        // format ngày cho thẻ input date
        $classRoom->input_start_date = Carbon::parse($classRoom->start_date)->format('Y-m-d');
        $classRoom->input_end_date = Carbon::parse($classRoom->end_date)->format('Y-m-d');

        return response()->json([
            'classRoom' => $classRoom
        ]);
    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, string $id)
    {
        // validate dữ liệu
        $request->validate([
            'course_id' => 'required',
            'teacher_id' => 'required',
            'shift_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'days_of_week' => 'required|array|min:1',
        ]);

        $classRoom = ClassRoom::findOrFail($id);

        try {
            // bật transaction
            DB::beginTransaction();

            $course = Course::findOrFail($request->course_id);
            $teacher = Teacher::findOrFail($request->teacher_id);
            $shift = Shift::findOrFail($request->shift_id);

            // lấy chuỗi giờ học
            $timeString = \Carbon\Carbon::parse($shift->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($shift->end_time)->format('H:i');

            // xử lý mảng thứ trong tuần
            $dayNumbers = [];
            $hasSunday = false;
            foreach ($request->days_of_week as $day) {
                if ($day == 8) {
                    $hasSunday = true;
                } else {
                    $dayNumbers[] = $day; 
                }
            }

            // format chuỗi thứ
            $daysString = count($dayNumbers) > 0 ? 'T' . implode(',', $dayNumbers) : '';
            if ($hasSunday) {
                $daysString = $daysString ? $daysString . ',CN' : 'CN';
            }

            // tạo tên lớp chuẩn
            $codeTeacher = $teacher->teacher_code;
            $newClassName = "{$course->name}-{$codeTeacher}-{$shift->name}({$daysString} {$timeString})";

            // cập nhật thông tin lớp
            $classRoom->update([
                'name' => $newClassName,
                'course_id' => $request->course_id,
                'teacher_id' => $request->teacher_id,
                'shift_id' => $request->shift_id,
                'room' => $request->room,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status
            ]);

            // làm mới khuôn mẫu lịch
            $classRoom->schedules()->delete();
            foreach ($request->days_of_week as $day) {
                ClassSchedule::create([
                    'class_room_id' => $classRoom->id,
                    'day_of_week' => $day
                ]);
            }

            // xoá các buổi học chưa diễn ra
            $classRoom->sessions()->where('status', 'scheduled')->delete();

            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $today = Carbon::today();

            // sinh lại lịch mới 
            for ($date = $start; $date->lte($end); $date->addDay()) {
                $dayVal = $date->dayOfWeekIso + 1;

                if (in_array($dayVal, $request->days_of_week)) {
                    // kiểm tra xem ngày này đã học chưa
                    $exists = ClassSession::where('class_room_id', $classRoom->id)
                        ->where('session_date', $date->format('Y-m-d'))
                        ->exists();

                    // chỉ tạo mới nếu chưa học và là ngày trong tương lai
                    if (!$exists && $date->gte($today)) {
                        ClassSession::create([
                            'class_room_id' => $classRoom->id,
                            'session_date' => $date->format('Y-m-d'),
                            'status' => 'scheduled'
                        ]);
                    }
                }
            }

            // xác nhận lưu
            DB::commit();
            return response()->json(['message' => 'cập nhật lớp và lịch học thành công!']);
        } catch (\Exception $e) {
            // hoàn tác nếu lỗi
            DB::rollBack();
            return response()->json(['message' => 'lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        // xoá mềm lớp học
        $class = ClassRoom::findOrFail($id);
        $class->delete(); 

        return response()->json(['message' => 'đã xoá lớp học vào thùng rác.']);
    }

    public function storeShift(Request $request)
    {
        // validate dữ liệu ca học
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // lưu ca học
        $shift = Shift::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'thêm ca học thành công!',
            'shift' => $shift,
            'formatted_time' => Carbon::parse($shift->start_time)->format('H:i') . ' - ' . Carbon::parse($shift->end_time)->format('H:i')
        ]);
    }

    public function destroyShift($id)
    {
        try {
            // xoá ca học
            Shift::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'đã xoá ca học.']);
        } catch (\Illuminate\Database\QueryException $e) {
            // chặn xoá nếu ca đang được sử dụng
            return response()->json([
                'success' => false,
                'message' => 'không thể xoá vì ca này đang được xếp cho lớp học!'
            ], 422);
        }
    }
}