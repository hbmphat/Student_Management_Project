<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Promotion extends Model
{
    //
    use LogsActivity;
    protected $fillable = ['name', 'discount_percent', 'description', 'is_active'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Theo dõi tất cả các cột trong $fillable
            ->logOnlyDirty() // Chỉ lưu những cột bị thay đổi (khi Update)
            ->setDescriptionForEvent(fn(string $eventName) => "Khuyến mãi đã bị {$eventName}");
    }
}
