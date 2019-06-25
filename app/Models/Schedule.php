<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'guard_id',
        'date',
        'start_time',
        'end_time',
    ];

    public function securityGuard()
    {
        return $this->belongsTo(Guard::class);
    }
}
