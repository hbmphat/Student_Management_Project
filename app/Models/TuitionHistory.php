<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuitionHistory extends Model
{
    //
    protected $fillable = [
        'tuition_id',
        'payment_id',
        'action_type',
        'days_added',
        'old_to_date',
        'new_to_date',
        'note',
        'created_by'
    ];

    public function tuition()
    {
        return $this->belongsTo(Tuition::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
