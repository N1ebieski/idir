<?php

namespace N1ebieski\IDir\Database\Seeders\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Category\Dir\Category;

class LinksSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::active()->get(['id'])->pluck('id')->toArray();

        Link::makeFactory()->count(10)->backlink()->create()
            ->each(function ($link) use ($categories) {
                shuffle($categories);
                $link->categories()->attach(array_slice($categories, 0, rand(1, 5)));
            });
    }
}