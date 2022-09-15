<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Database\Seeders\Env;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Link;
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
