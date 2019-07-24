<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Guard;
use Faker\Generator as Faker;

$factory->define(Guard::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'color_indicator' => $faker->unique()->hexColor,
    ];
});

