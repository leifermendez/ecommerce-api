<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\categories;
use Faker\Generator as Faker;

$factory->define(categories::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(2),
        'description' => $faker->sentence(8),
        'image' => rand(1, 10),
        'icon' => 'fa fa-beer',
        'child' => rand(1, 5),
        'order' => rand(1, 10),
    ];
});
