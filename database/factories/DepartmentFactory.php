<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laravue\Models\Department as Department;
use Faker\Generator as Faker;

$factory->define(Department::class, function (Faker $faker) {
    return [
        'name_ja' => $faker->name,
        'name_en' => $faker->name,
    ];
});
