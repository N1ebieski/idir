<?php

namespace N1ebieski\IDir\Seeds\Install;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

/**
 * [DefaultRolesAndPermissionsSeeder description]
 */
class DefaultRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
        // Delete because Version 1.3 introduced adding entries by non-logged guests        
        // Permission::firstOrCreate(['name' => 'web.dirs.create']);
        Permission::firstOrCreate(['name' => 'web.dirs.edit']);
        Permission::firstOrCreate(['name' => 'web.dirs.delete']);
        Permission::firstOrCreate(['name' => 'web.dirs.notification']);
    }
}
