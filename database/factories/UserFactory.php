<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('secret'),
        'phone' => $faker->phoneNumber,
        'confirmed' => rand(0, 1),
        'avatar' => 'http://lorempixel.com/640/480/',
        'header' => 'http://lorempixel.com/1200/680/',
        'role' => $faker->randomElement(['admin','user','seller','shop']),
        'referer_code' => $faker->swiftBicNumber,
        'remember_token' => Str::random(10),
    ];
});
