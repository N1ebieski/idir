<?php

namespace N1ebieski\IDir\Seeds;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\Models\Group;

/**
 * [GroupSeeder description]
 */
class DefaultGroupAndPrivilegesSeeder extends Seeder
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
        Privilege::create(['name' => 'additional options for editing content']);

        $default = Group::create([
            'id' => 1,
            'name' => 'Default',
            'max_cats' => 3,
            'position' => 0,
            'visible' => 0,
            'backlink' => 0,
            'apply_status' => 0,
            'url' => 1
        ]);
    }
}
