<?php

namespace N1ebieski\IDir\Database\Seeders\Install;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Privilege;

/**
 * [GroupSeeder description]
 */
class DefaultGroupAndPrivilegesSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        // create privileges
        Privilege::firstOrCreate(['name' => 'highest position on homepage']);
        Privilege::firstOrCreate(['name' => 'highest position in their categories']);
        Privilege::firstOrCreate(['name' => 'highest position in ancestor categories']);
        Privilege::firstOrCreate(['name' => 'highest position in search results']);
        Privilege::firstOrCreate(['name' => 'additional link on the friends subpage']);
        Privilege::firstOrCreate(['name' => 'direct link on listings']);
        Privilege::firstOrCreate(['name' => 'place in the links component']);
        Privilege::firstOrCreate(['name' => 'place in the advertising component']);
        Privilege::firstOrCreate(['name' => 'additional options for editing content']);

        $nofollow = Privilege::firstOrCreate(['name' => 'direct link nofollow']);

        $default = Group::firstOrCreate(['id' => 1], [
            'name' => 'Default',
            'max_cats' => 3,
            'position' => 0,
            'visible' => Group::INVISIBLE,
            'backlink' => Group::WITHOUT_BACKLINK,
            'apply_status' => Group::APPLY_INACTIVE,
            'url' => Group::OPTIONAL_URL
        ]);

        $default->privileges()->sync([$nofollow->id]);
    }
}
