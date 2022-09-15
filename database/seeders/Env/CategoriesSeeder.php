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
use N1ebieski\IDir\Models\Category\Dir\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        $pattern = [
            0 => 10,
            1 => [2, 10],
            2 => [0, 10],
            3 => [0, 5]
        ];

        $depth = 0;

        $closure = function ($parent_id) use ($pattern, &$closure, &$depth) {
            if (is_array($pattern[$depth])) {
                $loop = rand($pattern[$depth][0], $pattern[$depth][1]);
            } else {
                $loop = $pattern[$depth];
            }

            for ($i = 0; $i < $loop; $i++) {
                /** @var Category */
                $category = Category::makeFactory()->create([
                    'parent_id' => $parent_id
                ]);

                $depth = $category->real_depth + 1;

                if (isset($pattern[$depth])) {
                    $closure($category->id);
                }
            }
        };

        $closure(null);
    }
}
