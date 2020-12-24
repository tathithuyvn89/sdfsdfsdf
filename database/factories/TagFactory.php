<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Laravue\Models\Tag as Tag;
use Faker\Generator as Faker;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name_ja' => $faker->name,
        'name_en' => $faker->name,
    ];
});
