<?php

namespace N1ebieski\IDir\Database\Seeders\Env;

use Faker\Factory as Faker;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Category\Dir\Category;

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

        $group = Group::makeFactory()->public()->create();

        Dir::makeFactory()->count(50)->for($group)->for(User::find(1))
            ->titleSentence()
            ->contentText()
            ->create()
            ->each(function ($dir) use ($categories) {
                $dir->tag(Faker::create()->words(rand(1, 5)));
                shuffle($categories);
                $dir->categories()->attach(array_slice($categories, 0, rand(1, 5)));
            });
    }
}
