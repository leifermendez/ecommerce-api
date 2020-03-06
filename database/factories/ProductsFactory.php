<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\products;
use Faker\Generator as Faker;

$factory->define(products::class, function (Faker $faker) {
    return [
    	'name' => $faker->sentence(2),
        'short_description' => $faker->sentence(8),
        'featured' => $faker->randomElement(['premium', 'regular']),
        'product_type' => $faker->randomElement(['digital', 'physical']),
        'description' => $faker->sentence(20),
        'shop_id' => 1,
        'status' => $faker->randomElement(['available', 'unavailable']),
    ];
});
