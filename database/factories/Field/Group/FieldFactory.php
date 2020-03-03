<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Field\Group\Field;

$factory->define(Field::class, function (Faker $faker) {
    return [
        'title' => ucfirst($faker->unique()->word),
        'desc' => $faker->text(300),
        'visible' => rand(0, 1)
    ];
});

$factory->state(Field::class, 'public', function (Faker $faker) {
    return [
        'visible' => 1
    ];
});

$factory->state(Field::class, 'private', function (Faker $faker) {
    return [
        'visible' => 0
    ];
});

$factory->state(Field::class, 'input', function (Faker $faker) {
    return [
        'type' => 'input',
        'options' => [
            'min' => rand(3, 30),
            'max' => rand(100, 300),
            'required' => 1
        ]
    ];
});

$factory->state(Field::class, 'textarea', function (Faker $faker) {
    return [
        'type' => 'textarea',
        'options' => [
            'min' => rand(3, 30),
            'max' => rand(100, 3000),
            'required' => 1
        ]
    ];
});

$factory->state(Field::class, 'select', function (Faker $faker) {
    return [
        'type' => 'select',
        'options' => [
            'options' => $faker->words(5, false),
            'required' => 1
        ]
    ];
});

$factory->state(Field::class, 'multiselect', function (Faker $faker) {
    return [
        'type' => 'multiselect',
        'options' => [
            'options' => $faker->words(5, false),
            'required' => 1
        ]
    ];
});

$factory->state(Field::class, 'checkbox', function (Faker $faker) {
    return [
        'type' => 'checkbox',
        'options' => [
            'options' => $faker->words(5, false),
            'required' => 1
        ]
    ];
});

$factory->state(Field::class, 'image', function (Faker $faker) {
    return [
        'type' => 'image',
        'options' => [
            'width' => 720,
            'height' => 480,
            'size' => 2048,
            'required' => 1
        ]
    ];
});
