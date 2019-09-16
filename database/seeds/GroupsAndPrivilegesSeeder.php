<?php

namespace N1ebieski\IDir\Seeds;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Models\Group\Group;

/**
 * [GroupSeeder description]
 */
class GroupsAndPrivilegesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create privileges
        Privilege::create(['name' => 'highest position on homepage']);
        Privilege::create(['name' => 'highest position in their categories']);
        Privilege::create(['name' => 'highest position in ancestor categories']);
        Privilege::create(['name' => 'highest position in search results']);
        Privilege::create(['name' => 'additional link on the friends subpage']);
        Privilege::create(['name' => 'direct link nofollow']);
        Privilege::create(['name' => 'direct link on listings']);
        Privilege::create(['name' => 'place in the links component']);
        Privilege::create(['name' => 'place in the advertising component']);

        $standard = Group::create([
            'model_type' => 'N1ebieski\IDir\Models\Dir',
            'name' => 'Default',
            'max_categories' => 3,
            'position' => 0,
            'visible' => 0,
            'backlink' => 0
        ]);
    }
}
