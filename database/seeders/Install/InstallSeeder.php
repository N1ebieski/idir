<?php

namespace N1ebieski\IDir\Database\Seeders\Install;

use Illuminate\Database\Seeder;

class InstallSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefaultRolesAndPermissionsSeeder::class);
        $this->call(DefaultGroupAndPrivilegesSeeder::class);
        $this->call(DefaultFieldsSeeder::class);
        $this->call(DefaultGusFieldSeeder::class);
        $this->call(DefaultRegionsSeeder::class);
        $this->call(DefaultStatsSeeder::class);
    }
}
