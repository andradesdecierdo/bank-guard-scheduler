<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guard extends Model
{
    protected $fillable = [
        'name',
        'color_indicator',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
