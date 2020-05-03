<?php

use Faker\Generator as Faker;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\User;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Carbon\Carbon;
use Illuminate\Support\Str;

$factory->define(Dir::class, function (Faker $faker) {
    $url = parse_url($faker->url);
    $content = Str::random(350);

    return [
        // i cant use faker, because it doesnt have strict option to set min and max chars
        'title' => Str::random(rand(10, 30)),
        'content_html' => $content,
        'content' => $content,
        'url' => $url['scheme']."://".$url['host'],
        'status' => rand(0, 1),
    ];
});

$factory->state(Dir::class, 'title_sentence', function (Faker $faker) {
    return [
        'title' => $faker->sentence(rand(1, 3))
    ];
});

$factory->state(Dir::class, 'content_text', function (Faker $faker) {
    $content = $faker->text(350);

    return [
        'content_html' => $content,
        'content' => $content
    ];
});

$factory->state(Dir::class, 'with_user', function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->states('user')->create()->id
    ];
});

$factory->state(Dir::class, 'active', function (Faker $faker) {
    return [
        'status' => 1
    ];
});

$factory->state(Dir::class, 'backlink_inactive', function (Faker $faker) {
    return [
        'status' => 3
    ];
});

$factory->state(Dir::class, 'status_inactive', function (Faker $faker) {
    return [
        'status' => Dir::STATUS_INACTIVE
    ];
});

$factory->state(Dir::class, 'inactive', function (Faker $faker) {
    return [
        'status' => 0
    ];
});

$factory->state(Dir::class, 'pending', function (Faker $faker) {
    return [
        'status' => 2
    ];
});

$factory->state(Dir::class, 'paid_seasonal', function (Faker $faker) {
    return [
        'status' => 1,
        'privileged_at' => Carbon::now(),
        'privileged_to' => Carbon::now()->addDays(14)
    ];
});

$factory->state(Dir::class, 'without_url', function (Faker $faker) {
    return [
        'url' => null,
    ];
});

$factory->afterCreatingState(Dir::class, 'with_category', function ($dir) {
    $dir->categories()->sync(
        factory(Category::class)->states('active')->create()->id
    );
});
