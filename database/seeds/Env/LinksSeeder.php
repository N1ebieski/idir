<?php

namespace N1ebieski\IDir\Seeds\Env;

use Illuminate\Database\Seeder;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Category\Dir\Category;

/**
 * [LinksSeeder description]
 */
class LinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::active()->get(['id'])->pluck('id')->toArray();

        factory(Link::class, 10)->states('backlink')->create()
            ->each(function ($link) use ($categories) {
                shuffle($categories);
                $link->categories()->attach(array_slice($categories, 0, rand(1, 5)));
            });
    }
}
