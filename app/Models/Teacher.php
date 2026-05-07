<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity; 
use Spatie\Activitylog\LogOptions; 

class Teacher extends Model
{
    //
    use HasFactory, SoftDeletes, LogsActivity;
    protected $fillable = [
        'teacher_code',
        'name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'qualifications',
        'status',
    ];
    // Cấu hình lưu lại những gì
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Theo dõi tất cả các cột trong $fillable
            ->logOnlyDirty() // Chỉ lưu những cột bị thay đổi (khi Update)
            ->setDescriptionForEvent(fn(string $eventName) => "Giảng viên đã bị {$eventName}");
    }
}
