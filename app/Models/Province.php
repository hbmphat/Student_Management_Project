<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    // 1 tỉnh có nhiều quận/huyện
    public function districts()
    {
        return $this->hasMany(District::class);
    }
}