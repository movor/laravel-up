<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Tag::class, function (Faker $faker) {
    $name = $faker->unique()->word;

    // Add one more word sometimes
    $name = chance(30, function () use ($name, $faker) {
        return $name . ' ' . $faker->unique()->word;
    }, $name);

    return [
        'name' => ucfirst($name),
    ];
});