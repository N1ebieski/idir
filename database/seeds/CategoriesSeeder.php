<?php

namespace N1ebieski\IDir\Seeds;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Category\Dir\Category;

/**
 * [CategorySeeder description]
 */
class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
                $category = factory(Category::class)->create([
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
