<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Privilege;

$factory->define(Group::class, function (Faker $faker) {
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

$factory->afterCreatingState(Group::class, 'additional options for editing content', function ($group) {
    $group->privileges()->sync([Privilege::where('name', 'additional options for editing content')->first()->id]);
});

$factory->state(Group::class, 'apply_alt_deactivation', function (Faker $faker) {
    return [
        'alt_id' => null
    ];
});

$factory->state(Group::class, 'apply_alt_group', function (Faker $faker) {
    return [
        'alt_id' => 1
    ];
});

$factory->state(Group::class, 'apply_active', function (Faker $faker) {
    return [
        'apply_status' => 1
    ];
});

$factory->state(Group::class, 'apply_inactive', function (Faker $faker) {
    return [
        'apply_status' => 0
    ];
});

$factory->state(Group::class, 'required_backlink', function (Faker $faker) {
    return [
        'backlink' => 2
    ];
});

$factory->state(Group::class, 'without_url', function (Faker $faker) {
    return [
        'url' => 0
    ];
});

$factory->state(Group::class, 'required_url', function (Faker $faker) {
    return [
        'url' => 2
    ];
});

$factory->state(Group::class, 'max_models', function (Faker $faker) {
    return [
        'max_models' => 1
    ];
});

$factory->state(Group::class, 'max_cats', function (Faker $faker) {
    return [
        'max_cats' => 1
    ];
});

$factory->state(Group::class, 'max_models_daily', function (Faker $faker) {
    return [
        'max_models_daily' => 1
    ];
});

$factory->state(Group::class, 'public', function (Faker $faker) {
    return [
        'visible' => 1
    ];
});

$factory->state(Group::class, 'private', function (Faker $faker) {
    return [
        'visible' => 0
    ];
});
