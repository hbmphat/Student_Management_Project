<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuitionPackage extends Model
{
    //
    protected $fillable = ['name', 'weeks', 'price', 'is_active'];
}
