<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\shop;
use Faker\Generator as Faker;

$factory->define(shop::class, function (Faker $faker) {
    return [
        'users_id' => rand(1, 12),
        'name'=> $faker->company,
        'address' => $faker->address,
        'slug' => $faker->slug,
        'zip_code' => $faker->postcode,
        'legal_id' => $faker->ean8,
        'email_corporate' => $faker->companyEmail,
        'image_cover' => rand(1, 12),
        'image_header' => rand(1, 12),
        'phone_mobil' => $faker->phoneNumber,
        'phone_fixed' => $faker->phoneNumber,
        'meta_key' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'terms_conditions' => $faker->text($maxNbChars = 190),
        'polity_privacy' => $faker->text($maxNbChars = 190)
    ];
});
