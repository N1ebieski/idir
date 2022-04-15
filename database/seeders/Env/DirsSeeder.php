<?php

namespace N1ebieski\IDir\Database\Seeders\Env;

use Faker\Factory as Faker;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Category\Dir\Category;

/**
 * [DirsSeeder description]
 */
class DirsSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::active()->get(['id'])->pluck('id')->toArray();

        $group = factory(Group::class)->states('public')->create();

        factory(Dir::class, 50)->states(['title_sentence', 'content_text'])
            ->make()
            ->each(function ($dir) use ($categories, $group) {
                $dir->user()->associate(1);
                $dir->group()->associate($group);
                $dir->save();

                $dir->tag(Faker::create()->words(rand(1, 5)));
                shuffle($categories);
                $dir->categories()->attach(array_slice($categories, 0, rand(1, 5)));
            });
    }
}
