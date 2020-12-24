<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laravue\Models\Quiz as Quiz;
use Faker\Generator as Faker;

$factory->define(Quiz::class, function (Faker $faker) {
    return [
        'title_ja' => $faker->sentence(),
        'title_en' => $faker->sentence(),
        'sentence_ja' => $faker->sentence(),
        'sentence_en' => $faker->sentence(),
        'picture_ja'=> $faker->imageUrl($width = 200, $height = 400),
        'picture_en'=> $faker->imageUrl($width = 200, $height = 400),
        'explain_correct_ja' => $faker->sentence(),
        'explain_correct_en' => $faker->sentence(),
        'explain_incorrect_ja' => $faker->sentence(),
        'explain_incorrect_en' => $faker->sentence(),
        'admin_created' => App\Laravue\Models\User::all()->random()->id,
    ];
});
