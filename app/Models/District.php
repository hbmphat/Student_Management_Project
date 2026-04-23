<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'province_id'];

    // thuộc về 1 tỉnh
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // có nhiều phường/xã
    public function wards()
    {
        return $this->hasMany(Ward::class);
    }
}