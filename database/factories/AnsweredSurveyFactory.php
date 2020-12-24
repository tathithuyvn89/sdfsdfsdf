<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laravue\Models\AnsweredSurvey as AnsweredSurvey;
use Faker\Generator as Faker;

$factory->define(AnsweredSurvey::class, function (Faker $faker) {
    return [
        'respondent_id' => App\Laravue\Models\User::all()->random()->id,
        'survey_id' => App\Laravue\Models\Survey::all()->random()->id,
        'result' => $faker->numberBetween($min = 1, $max = 10)
    ];
});

