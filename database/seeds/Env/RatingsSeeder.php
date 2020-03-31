<?php

namespace N1ebieski\IDir\Seeds\Env;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Dir;

/**
 * [RatingsSeeder description]
 */
class RatingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dirs = Dir::all();

        $dirs->chunk(1000, function ($items) {
            $items->each(function ($item) {
                for ($i = 0; $i < rand(1, 10); $i++) {
                    $item->ratings()->create([
                        'user_id' => 1,
                        'rating' => rand(1, 5)
                    ]);
                }
            });
        });
    }
}
