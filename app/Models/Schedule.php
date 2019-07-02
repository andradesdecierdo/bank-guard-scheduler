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

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function securityGuard()
    {
        return $this->belongsTo(Guard::class);
    }
}
