<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\purchase_order;
use Faker\Generator as Faker;

$factory->define(purchase_order::class, function (Faker $faker) {
    return [
        'uuid' => $faker->uuid,
        'uuid_shipping' => $faker->md5,
        'amount' => $faker->randomFloat(2,1,9999),
        'feed' => $faker->randomFloat(2,2,20),
        'amount_shipping' => $faker->randomFloat(2,2,20),
        'user_id' => 2,
        'shop_id' => 1,
        'shipping_address_id' => rand(1, 10),
        'status' => $faker->randomElement(['success', 'cancel']),
    ];
});
