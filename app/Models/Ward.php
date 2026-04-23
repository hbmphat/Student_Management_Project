<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'district_id'];

    // thuộc về 1 quận/huyện
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // 1 phường có thể có nhiều học viên sinh sống
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}