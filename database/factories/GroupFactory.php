<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Group;

$factory->define(Group::class, function(Faker $faker) {
    return [
        'name' => ucfirst($faker->unique()->word),
        'desc' => $faker->text(300),
        'max_cats' => rand(1, 5),
        'max_models' => $faker->randomElement([rand(10, 50), null]),
        'max_models_daily' => $faker->randomElement([rand(5, 10), null]),
        'visible' => rand(0, 1),
        'apply_status' => rand(0, 1),
        'backlink' => rand(0, 1),
        'url' => rand(0, 2)
    ];
});
