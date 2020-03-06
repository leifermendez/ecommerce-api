<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\category_attributes;
use Faker\Generator as Faker;

$factory->define(category_attributes::class, function (Faker $faker) {
    return [
        'category_id' => rand(1, 10),
        'attributes_id' => rand(1, 10),
    ];
});
