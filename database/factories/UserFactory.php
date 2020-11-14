<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => str_replace("'", '', $faker->unique()->name),
        'ip' => $faker->ipv4,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
        'status' => rand(0, 1),
    ];
});

$factory->state(User::class, 'active', function (Faker $faker) {
    return [
        'status' => 1
    ];
});

$factory->afterCreatingState(User::class, 'user', function ($user) {
    $user->assignRole('user');
});

$factory->afterCreatingState(User::class, 'admin', function ($user) {
    $user->assignRole('admin');
});

$factory->afterCreatingState(User::class, 'super-admin', function ($user) {
    $user->assignRole('super-admin');
});

$factory->afterCreatingState(User::class, 'ban_user', function ($user) {
    $user->ban()->create();
});
