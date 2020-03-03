<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Code;

$factory->define(Code::class, function (Faker $faker) {
    return [
        'code' => $faker->unique()->word,
        'quantity' => rand(0, 20),
    ];
});

$factory->state(Code::class, 'one', function (Faker $faker) {
    return [
        'quantity' => 1
    ];
});

$factory->state(Code::class, 'two', function (Faker $faker) {
    return [
        'quantity' => 2
    ];
});
