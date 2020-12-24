<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laravue\Models\Survey as Survey;
use Faker\Generator as Faker;

$factory->define(Survey::class, function (Faker $faker) {
    return [
        'title_ja' => $faker->sentence(),
        'title_en' => $faker->sentence(),
        'display' => $faker->randomElement([0,1]),
        'status' => $faker->randomElement([0,1]),
        'correct_pic'=> $faker->imageUrl($width = 200, $height = 200),
        'incorrect_pic'=> $faker->imageUrl($width = 200, $height = 200),
        'completemessage_ja' => $faker->sentence(),
        'completemessage_en' => $faker->sentence(),
        'department_id' => App\Laravue\Models\Department::all()->random()->id,
        'classs_id' => App\Laravue\Models\Classs::all()->random()->id,
        'admin_created' => App\Laravue\Models\User::all()->random()->id,
    ];;
});
