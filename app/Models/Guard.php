<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guard extends Model
{
    protected $fillable = [
        'name',
        'color_indicator',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
