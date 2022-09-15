<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
