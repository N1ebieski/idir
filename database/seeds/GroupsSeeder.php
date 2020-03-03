<?php

namespace N1ebieski\IDir\Seeds;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Models\Group;

/**
 * [GroupSeeder description]
 */
class GroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $privileges = Privilege::all();

        factory(Group::class, 5)
            ->create()
            ->each(function ($group) use ($privileges) {
                $group->privileges()->attach(
                    $privileges->random(rand(0, $privileges->count()))
                        ->pluck('id')->toArray()
                );
            });
    }
}
