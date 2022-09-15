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

use N1ebieski\IDir\Models\Dir;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Collection;

class RatingsSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        Dir::chunk(1000, function (Collection $dirs) {
            $dirs->each(function (Dir $dir) {
                for ($i = 0; $i < rand(1, 10); $i++) {
                    $dir->ratings()->create([
                        'user_id' => 1,
                        'rating' => rand(1, 5)
                    ]);
                }
            });
        });
    }
}
