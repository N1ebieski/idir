<?php

namespace N1ebieski\IDir\Seeds;

use Illuminate\Database\Seeder;

/**
 * [DatabaseSeeder description]
 */
class EnvSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(DefaultGroupAndPrivilegesSeeder::class);
        $this->call(GroupsSeeder::class);
        $this->call(LinksSeeder::class);
        $this->call(DefaultFieldsSeeder::class);
        $this->call(RegionsSeeder::class);
    }
}
