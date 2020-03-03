<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Payment\Dir\Payment;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(Payment::class, 'pending', function (Faker $faker) {
    return [
        'status' => 2
    ];
});
