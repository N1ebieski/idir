<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\User;

$factory->define(Dir::class, function(Faker $faker) {
    return [
        'title' => ucfirst($faker->unique()->word),
        'content_html' => $faker->text(300),
        'content' => $faker->text(300),
        'url' => $faker->url,
        'status' => rand(0, 1),

    ];
});

$factory->state(Dir::class, 'with_user', function(Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id
    ];
});

$factory->state(Dir::class, 'pending', function(Faker $faker) {
    return [
        'status' => 2
    ];
});
