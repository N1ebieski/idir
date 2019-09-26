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
        for ($i=0; $i<10; $i++) {
            $cat[$i] = factory(Category::class)->make();
            $cat[$i]->save();

            for ($j=0; $j<rand(2, 10); $j++) {
                $catcat[$j] = factory(Category::class)->make();
                $catcat[$j]->parent_id = $cat[$i]->id;
                $catcat[$j]->save();

                for ($k=0; $k<rand(0, 10); $k++) {
                    $catcatcat[$k] = factory(Category::class)->make();
                    $catcatcat[$k]->parent_id = $catcat[$j]->id;
                    $catcatcat[$k]->save();

                    if (rand(0, 1) == 1) {
                        for ($l=0; $l<rand(0, 5); $l++) {
                            $catcatcatcat[$l] = factory(Category::class)->make();
                            $catcatcatcat[$l]->parent_id = $catcatcat[$k]->id;
                            $catcatcatcat[$l]->save();
                        }
                    }
                }
            }
        }
    }
}
