<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Guard;
use App\Models\Schedule;
use Faker\Generator as Faker;

$factory->define(Schedule::class, function (Faker $faker) {
    return [
        'date' => $faker->date('Y-m-d'),
        'guard_id' => function () {
            return factory(Guard::class)->create()->id;
        },
    ];
});
