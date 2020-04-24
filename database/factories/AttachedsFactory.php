<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\attached;
use Faker\Generator as Faker;

$factory->define(attached::class, function (Faker $faker) {
    return [
        'name' => Str::random(20),
        'users_id' => rand(1, 3),
        'small' => $faker->imageUrl(250,120,'cats'),
        'medium' => $faker->imageUrl(480,240,'cats'),
        'large' => $faker->imageUrl(800,480,'cats'),
        'media_type' => 'image'
    ];
});
