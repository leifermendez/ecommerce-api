<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\variation_product;
use Faker\Generator as Faker;

$factory->define(variation_product::class, function (Faker $faker) {
    return [
        'price_normal' => $faker->randomFloat(2, 100, 120),
        'price_regular' => $faker->randomFloat(2, 121, 400),
        'product_id' => rand(1, 5),
        'quantity' => $faker->numberBetween(1, 5),
        'label' => $faker->sentence(2),
        'attached_id' => rand(1, 30),
        'observation' => $faker->sentence(5),
        'weight' => $faker->numberBetween(20, 60),
        'width' => $faker->numberBetween(15, 60),
        'height' => $faker->numberBetween(20, 60),
        'length' => $faker->numberBetween(20, 60),
        'delivery' => rand(1, 0),
    ];
});
