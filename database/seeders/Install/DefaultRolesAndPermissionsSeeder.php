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
use Spatie\Permission\Models\Permission;

class DefaultRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        // create permdissions
        Permission::firstOrCreate(['name' => 'admin.groups.*']);
        Permission::firstOrCreate(['name' => 'admin.groups.view']);
        Permission::firstOrCreate(['name' => 'admin.groups.create']);
        Permission::firstOrCreate(['name' => 'admin.groups.edit']);
        Permission::firstOrCreate(['name' => 'admin.groups.delete']);

        Permission::firstOrCreate(['name' => 'admin.prices.*']);
        Permission::firstOrCreate(['name' => 'admin.prices.view']);
        Permission::firstOrCreate(['name' => 'admin.prices.create']);
        Permission::firstOrCreate(['name' => 'admin.prices.edit']);
        Permission::firstOrCreate(['name' => 'admin.prices.delete']);

        Permission::firstOrCreate(['name' => 'admin.fields.*']);
        Permission::firstOrCreate(['name' => 'admin.fields.view']);
        Permission::firstOrCreate(['name' => 'admin.fields.create']);
        Permission::firstOrCreate(['name' => 'admin.fields.edit']);
        Permission::firstOrCreate(['name' => 'admin.fields.delete']);

        Permission::firstOrCreate(['name' => 'admin.dirs.*']);
        Permission::firstOrCreate(['name' => 'admin.dirs.view']);
        Permission::firstOrCreate(['name' => 'admin.dirs.create']);
        Permission::firstOrCreate(['name' => 'admin.dirs.status']);
        Permission::firstOrCreate(['name' => 'admin.dirs.edit']);
        Permission::firstOrCreate(['name' => 'admin.dirs.delete']);
        Permission::firstOrCreate(['name' => 'admin.dirs.notification']);

        Permission::firstOrCreate(['name' => 'web.dirs.*']);
        Permission::firstOrCreate(['name' => 'web.dirs.create']);
        Permission::firstOrCreate(['name' => 'web.dirs.edit']);
        Permission::firstOrCreate(['name' => 'web.dirs.delete']);
        Permission::firstOrCreate(['name' => 'web.dirs.notification']);

        Permission::firstOrCreate(['name' => 'api.groups.*']);
        Permission::firstOrCreate(['name' => 'api.groups.view']);

        Permission::firstOrCreate(['name' => 'api.dirs.*']);
        Permission::firstOrCreate(['name' => 'api.dirs.view']);
        Permission::firstOrCreate(['name' => 'api.dirs.create']);
        Permission::firstOrCreate(['name' => 'api.dirs.status']);
        Permission::firstOrCreate(['name' => 'api.dirs.edit']);
        Permission::firstOrCreate(['name' => 'api.dirs.delete']);
    }
}
