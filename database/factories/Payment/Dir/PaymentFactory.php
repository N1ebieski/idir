<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Payment\Dir\Payment;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(Payment::class, 'pending', function (Faker $faker) {
    return [
        'status' => Payment::PENDING
    ];
});

$factory->afterMakingState(Payment::class, 'with_morph', function ($payment) {
    $payment->morph()->associate(
        factory(Dir::class)->states(['title_sentence', 'content_text', 'with_user', 'pending', 'with_category', 'with_default_group'])->create()
    );
});

$factory->afterMakingState(Payment::class, 'with_order', function ($payment) {
    $payment->orderMorph()->associate(
        factory(Price::class)->states(['transfer', 'with_group'])->create()
    );
});
