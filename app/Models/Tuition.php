<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuition extends Model
{
    //
    protected $fillable = ['student_id', 'class_room_id', 'from_date', 'to_date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
    public function histories()
    {
        return $this->hasMany(TuitionHistory::class);
    }
}
