<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\attributes;
use Faker\Generator as Faker;

$factory->define(attributes::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(2),
        'element_type' =>  $faker->randomElement(['text','number','select','textarea','checkbox']),
        'required' => rand(0, 1),
    ];
});
