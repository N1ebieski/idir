<?php

namespace N1ebieski\IDir\Database\Seeders\Env;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Privilege;

/**
 * [GroupSeeder description]
 */
class GroupsSeeder extends Seeder
{
    /**
     * Run the database Seeders.
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
