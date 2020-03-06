<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\attached_products;
use Faker\Generator as Faker;

$factory->define(attached_products::class, function (Faker $faker) {
    return [
        'attached_id' => rand(1, 30),
        'product_id' => $faker->unique()->numberBetween(1,5),
        
    ];
});
