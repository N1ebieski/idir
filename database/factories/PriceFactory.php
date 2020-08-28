<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Price;

$factory->define(Price::class, function (Faker $faker) {
    return [
        'type' => $faker->randomElement(['transfer', 'code_transfer', 'code_sms']),
        'price' => number_format(rand(12, 57) / 10, 2),
        'days' => $faker->randomElement([rand(7, 365), null]),
    ];
});

$factory->state(Price::class, 'transfer', function (Faker $faker) {
    return [
        'type' => 'transfer'
    ];
});

$factory->state(Price::class, 'code_sms', function (Faker $faker) {
    return [
        'type' => 'code_sms',
        'number' => 99999,
        'code' => 'XX.XXX',
        'token' => 'c78zs8ds8ds'
    ];
});

$factory->state(Price::class, 'code_transfer', function (Faker $faker) {
    return [
        'type' => 'code_transfer',
        'code' => 'dasdasdasd'
    ];
});

$factory->state(Price::class, 'seasonal', function (Faker $faker) {
    return [
        'days' => rand(7, 365)
    ];
});
