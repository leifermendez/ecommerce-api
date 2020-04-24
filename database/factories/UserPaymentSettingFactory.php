<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\user_payment;
use Faker\Generator as Faker;

$factory->define(user_payment::class, function (Faker $faker) {
    return [
        'user_id' => rand(1, 3),
        'payment_option' => 'stripe',
        'payment_email' => 'pagos@compnay.com',
        'iban' => $faker->ean8,
        'observation' => 'Alguna observacion',
        'account_name' => $faker->name,
        'account_lastname' => $faker->lastName,
        'primary' => 1
    ];
});
