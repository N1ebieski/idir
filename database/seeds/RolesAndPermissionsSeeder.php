<?php

namespace N1ebieski\IDir\Seeds;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use N1ebieski\ICore\Seeds\RolesAndPermissionsSeeder as BaseRolesAndPermissionsSeeder;

/**
 * [RolesAndPermissionsSeeder description]
 */
class RolesAndPermissionsSeeder extends BaseRolesAndPermissionsSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //parent::run();

        // create permdissions
        Permission::create(['name' => 'index groups']);
        Permission::create(['name' => 'create groups']);
        Permission::create(['name' => 'edit groups']);
        Permission::create(['name' => 'destroy groups']);

        $role = Role::whereName('admin')
            ->givePermissionTo([
                'index groups',
                'create groups',
                'edit groups',
                'destroy groups'
            ]);
    }
}
