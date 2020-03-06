<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\comments;
use Faker\Generator as Faker;

$factory->define(comments::class, function (Faker $faker) {
    return [
    	'user_id' => rand(1, 3),
        'product_id' => rand(1, 5),
        'shop_id' => 1,
        'purchase_id' => 1,
        'attached_id' => rand(1, 30),
        'score' => 3,
        'comment' => $faker->sentence(8)
    ];
});
