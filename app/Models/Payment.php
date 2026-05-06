<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
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
