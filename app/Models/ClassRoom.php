<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'course_id',
        'teacher_id',
        'shift_id',
        'room',
        'start_date',
        'end_date',
        'status'
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function sessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student')
            ->withPivot('id', 'status')
            ->withTimestamps();
    }
}
