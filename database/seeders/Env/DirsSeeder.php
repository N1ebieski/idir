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
        $categories = Category::active()->pluck('id')->toArray();

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var User */
        $user = User::find(1);

        Dir::makeFactory()->count(50)->for($group)->for($user)
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
