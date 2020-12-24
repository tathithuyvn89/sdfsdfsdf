<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laravue\Models\Classs as Classs;
use Faker\Generator as Faker;

$factory->define(Classs::class, function (Faker $faker) {
    return [
        'name_ja' => $faker->name,
        'name_en' => $faker->name,
    ];
});
