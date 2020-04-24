<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\purchase_detail;
use Faker\Generator as Faker;

$factory->define(purchase_detail::class, function (Faker $faker) {
    return [
        'purchase_uuid' => $faker->uuid,
        'product_id' => rand(1, 5),
        'product_qty' => $faker->numberBetween(1,5),
        'product_label' => $faker->company,
        'product_amount' => $faker->randomFloat(2,2,20),
        'shop_id' => 1,
    ];
});
