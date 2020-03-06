<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\shipping_address;
use Faker\Generator as Faker;

$factory->define(shipping_address::class, function (Faker $faker) {
    return [
        'user_id' => 2,
        'country' => $faker->countryCode,
        'state' => $faker->state,
        'district' => $faker->city,
        'address' => $faker->streetAddress,
        'zip_code' => '28039',
        'instructions' => 'Tocar el timbre 3 veces'
    ];
});
