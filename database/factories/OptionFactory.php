<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laravue\Models\Option as Option;
use Faker\Generator as Faker;

$factory->define(Option::class, function (Faker $faker) {
    return [
        'quiz_id' => App\Laravue\Models\Quiz::all()->random()->id,
        'option_ja' => $faker->sentence(),
        'option_en' => $faker->sentence(),
        'correct' => $faker->randomElement([0,1]),
        'position' => $faker->randomElement([1,2,3,4,5,6]),
    ];
});
