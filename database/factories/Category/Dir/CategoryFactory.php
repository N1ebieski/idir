<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Category\Dir\Category;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => ucfirst($faker->word),
        'status' => rand(Category::INACTIVE, Category::ACTIVE)
    ];
});

$factory->state(Category::class, 'active', function (Faker $faker) {
    return [
        'status' => Category::ACTIVE
    ];
});

$factory->state(Category::class, 'sentence', function (Faker $faker) {
    return [
        'name' => ucfirst($faker->word . ' ' . $faker->word)
    ];
});
