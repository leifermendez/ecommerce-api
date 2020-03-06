<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\orders;
use Faker\Generator as Faker;

$factory->define(orders::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(2,1,9999),
        'description' => $faker->sentence(8),
        'user_id' => 2,
        'observation' => $faker->sentence(10),
        'purchase_id' => 1,
        'platform_id' => $faker->numberBetween(1,3),
        'status' => $faker->randomElement(['wait', 'success']),
    ];
});
