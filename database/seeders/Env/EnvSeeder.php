<?php

namespace N1ebieski\IDir\Database\Seeders\Env;

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
        $this->call(\N1ebieski\IDir\Database\Seeders\Install\DefaultRolesAndPermissionsSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(\N1ebieski\IDir\Database\Seeders\Install\DefaultGroupAndPrivilegesSeeder::class);
        $this->call(GroupsSeeder::class);
        $this->call(LinksSeeder::class);
        $this->call(\N1ebieski\IDir\Database\Seeders\Install\DefaultFieldsSeeder::class);
        $this->call(\N1ebieski\IDir\Database\Seeders\Install\DefaultRegionsSeeder::class);
    }
}
