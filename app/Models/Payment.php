<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; 
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
    //
    use LogsActivity;
    protected $fillable = [
        'student_id',
        'class_room_id',
        'promotion_id',
        'paid_weeks',
        'receipt_code',
        'original_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
        'is_used',
        'created_by'
    ];

        public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Theo dõi tất cả các cột trong $fillable
            ->logOnlyDirty() // Chỉ lưu những cột bị thay đổi (khi Update)
            ->setDescriptionForEvent(fn(string $eventName) => "Thanh toán đã bị {$eventName}");
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
