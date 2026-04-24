<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommitmentReminder extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'start_date',
        'months',
        'end_date',
        'remaining_days',
        'ip',
        'kvkk_approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}
