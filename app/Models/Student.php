<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'dob', 'gender', 'parent_phone', 'parent_email', 
        'street_address', 'ward_id', 'avatar', 'face_image', 'status'
    ];

    // 1. TỰ ĐỘNG SINH UUID (HV000001) TRƯỚC KHI LƯU VÀO DB
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            // Tìm học viên được tạo cuối cùng (kể cả đã bị xóa mềm) để lấy mã
            $latestStudent = static::withTrashed()->orderBy('id', 'desc')->first();

            if (!$latestStudent) {
                $student->uuid = 'HV000001';
            } else {
                // Cắt lấy phần số (bỏ chữ HV), cộng thêm 1, rồi pad số 0 cho đủ 6 số
                $number = intval(substr($latestStudent->uuid, 2)) + 1;
                $student->uuid = 'HV' . str_pad($number, 6, '0', STR_PAD_LEFT);
            }
        });
        // 2. TỰ ĐỘNG ĐỒNG BỘ TRẠNG THÁI VÀO BẢNG LỚP HỌC (Code mới thêm vào)
        static::updated(function ($student) {
            // Kiểm tra xem cột 'status' có thực sự bị thay đổi hay không
            if ($student->wasChanged('status')) {
                
                // Cập nhật bảng trung gian (class_student)
                // CHÚ Ý: Chỉ cập nhật những lớp chưa kết thúc (khác 'completed')
                \Illuminate\Support\Facades\DB::table('class_student')
                    ->where('student_id', $student->id)
                    ->where('status', '!=', 'completed')
                    ->update([
                        'status' => $student->status,
                        'updated_at' => now() // Cập nhật luôn thời gian sửa
                    ]);
            }
        });
    }

    // 2. LOGIC HIỂN THỊ ẢNH THÔNG MINH (Accessor)
    // Cách gọi ở View: $student->display_avatar
    // LOGIC HIỂN THỊ ẢNH THÔNG MINH (Accessor)
    public function getDisplayAvatarAttribute()
    {
        // Ưu tiên 1: Lấy ảnh Avatar nếu Admin có upload
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Ưu tiên 2: Lấy ảnh Khuôn mặt (Sau này tích hợp Face ID)
        if ($this->face_image) {
            return asset('storage/' . $this->face_image);
        }

        // Ưu tiên 3: Tự động tạo ảnh có chữ cái đầu của tên (Ví dụ: Minh Triết -> MT)
        $encodedName = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$encodedName}&background=random&color=fff&size=150&font-size=0.4";
    }

    // Quan hệ với lớp học (Nhiều - Nhiều qua bảng class_student)
    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student', 'student_id', 'class_room_id')
            ->withPivot('status')
            ->withTimestamps();
    }
    // Mối quan hệ với bảng Wards
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    // Accessor: Tự động ghép chuỗi địa chỉ đầy đủ (Ví dụ: 93/2 Phạm Thế Hiển, Phường 4, Quận 8, TP.HCM)
    // Cách gọi ở View: $student->full_address
    public function getFullAddressAttribute()
    {
        if (!$this->ward) {
            return $this->street_address ?? 'Chưa cập nhật';
        }

        $wardName = $this->ward->name;
        $districtName = $this->ward->district->name;
        $provinceName = $this->ward->district->province->name;

        $street = $this->street_address ? $this->street_address . ', ' : '';

        return "{$street}{$wardName}, {$districtName}, {$provinceName}";
    }
}
