<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Group\Dir\Group;

$factory->define(Group::class, function(Faker $faker) {
    return [
        'name' => ucfirst($faker->word),
        'desc' => $faker->text(300),
        'max_cats' => rand(1, 5),
        'visible' => rand(0, 1),
        'backlink' => rand(0, 1),
        'days' => rand(30, 365),
        'max_dirs' => $faker->randomElement([rand(10, 50), null]),
        'max_dirs_daily' => $faker->randomElement([rand(5, 10), null])
    ];
});
