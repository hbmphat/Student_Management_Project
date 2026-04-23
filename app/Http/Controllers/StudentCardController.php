<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentCardController extends Controller
{
    public function generateCard($id)
    {
        $student = Student::with([
            'ward.district.province',
            'classRooms' => function ($query) {
                $query->with(['course', 'teacher', 'shift'])
                    ->orderByPivot('created_at', 'desc');
            },
        ])->findOrFail($id);

        $classRoom = $student->classRooms->first();

        $studentData = [
            'name' => $student->name,
            'birthday' => $student->dob ? Carbon::parse($student->dob)->format('d/m/Y') : 'Chưa cập nhật',
            'avatar' => $this->resolveAvatarPath($student),
            'logo' => $this->resolveLogoPath(),
            'level' => $classRoom?->course?->name ?? 'Chưa xếp lớp',
            'teacher_name' => $classRoom?->teacher?->name ?? 'Chưa cập nhật',
            'teacher_code' => $classRoom?->teacher?->teacher_code ?? 'N/A',
            'shift' => $classRoom?->shift?->name ?? 'Chưa cập nhật',
            'time' => $classRoom?->shift ? trim(($classRoom->shift->start_time ?? '') . ' - ' . ($classRoom->shift->end_time ?? ''), ' -') : 'Chưa cập nhật',
            'uuid' => $student->uuid,
            'phone' => $student->parent_phone ?? 'Chưa cập nhật',
            'address' => $student->full_address,
            'class_name' => $classRoom?->name ?? 'Chưa xếp lớp',
        ];

        $qrCode = base64_encode(
            QrCode::format('svg')->size(110)->margin(1)->generate($student->uuid)
        );

        $pdf = Pdf::loadView('students.print_card', [
            'student' => $studentData,
            'qrCode' => $qrCode,
        ])->setPaper([0, 0, 153, 244], 'landscape');

        return $pdf->stream('the-hoc-vien-' . $student->uuid . '.pdf');
    }

    private function resolveAvatarPath(Student $student): string
    {
        if ($student->avatar) {
            $path = str_replace('\\', '/', Storage::disk('public')->path($student->avatar));

            if (is_file($path)) {
                return $path;
            }
        }

        $sampleAvatar = public_path('images/avatar-sample.jpg');

        if (is_file($sampleAvatar)) {
            return $sampleAvatar;
        }

        return $this->placeholderAvatar($student->name);
    }

    private function resolveLogoPath(): string
    {
        $logoPath = public_path('images/logo.png');

        if (is_file($logoPath)) {
            return $logoPath;
        }

        return $this->placeholderAvatar('EngBreak');
    }

    private function placeholderAvatar(string $name): string
    {
        $initials = collect(preg_split('/\s+/', trim($name)))
            ->filter()
            ->take(2)
            ->map(function ($part) {
                return mb_substr($part, 0, 1, 'UTF-8');
            })
            ->implode('');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="380" viewBox="0 0 300 380">'
            . '<defs><linearGradient id="g" x1="0" x2="1" y1="0" y2="1"><stop offset="0%" stop-color="#0b3d91"/><stop offset="100%" stop-color="#1d66d1"/></linearGradient></defs>'
            . '<rect width="300" height="380" rx="24" fill="url(#g)"/>'
            . '<circle cx="150" cy="136" r="66" fill="rgba(255,255,255,0.18)"/>'
            . '<text x="150" y="150" text-anchor="middle" font-family="DejaVu Sans, Arial, sans-serif" font-size="54" font-weight="700" fill="#ffffff">' . e($initials ?: 'HV') . '</text>'
            . '<text x="150" y="250" text-anchor="middle" font-family="DejaVu Sans, Arial, sans-serif" font-size="24" font-weight="700" fill="#ffffff">HỌC VIÊN</text>'
            . '<text x="150" y="286" text-anchor="middle" font-family="DejaVu Sans, Arial, sans-serif" font-size="16" fill="#e8f1ff">EngBreak</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}