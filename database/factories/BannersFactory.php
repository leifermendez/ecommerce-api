<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\banners;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(banners::class, function (Faker $faker) {
	$now = Carbon::now();
    return [
        'attached_id' => rand(1, 30),
        'shop_id' => 1,
        'title' => $faker->sentence(2),
        'description' => $faker->sentence(8),
        'media_type' => $faker->randomElement(['image','video']),
        'start' => $now->toDateTimeString(),
        'finish' => $now->addMonth()->toDateTimeString(),
        'url' => $faker->url
    ];
});
