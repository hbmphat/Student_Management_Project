<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity; 
use Spatie\Activitylog\LogOptions;

class Course extends Model
{
    //
    use HasFactory, SoftDeletes, LogsActivity;
    protected $fillable = [
        'name',
        'duration_months',
        'weekly_price',
        'description'
    ];
        public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Theo dõi tất cả các cột trong $fillable
            ->logOnlyDirty() // Chỉ lưu những cột bị thay đổi (khi Update)
            ->setDescriptionForEvent(fn(string $eventName) => "Khóa học đã bị {$eventName}");
    }
}
