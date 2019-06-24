<?php

use Illuminate\Database\Seeder;
use App\Models\Guard;

class GuardSeeder extends Seeder
{
    const GUARDS = [
        [
            'name' => 'Andrade Decierdo',
            'color_indicator' => '#4286f4'
        ],
        [
            'name' => 'Chris Sarenas',
            'color_indicator' => '#7c0954'
        ],
        [
            'name' => 'John Doe',
            'color_indicator' => '#30705d'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::GUARDS as $guard) {
            $newGuard = new Guard([
                'name' => $guard['name'],
                'color_indicator' => $guard['color_indicator'],
            ]);
            $newGuard->save();
        }
    }
}
