<?php

namespace N1ebieski\IDir\Seeds;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

/**
 * [RolesAndPermissionsSeeder description]
 */
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create permdissions
        Permission::create(['name' => 'index groups']);
        Permission::create(['name' => 'create groups']);
        Permission::create(['name' => 'edit groups']);
        Permission::create(['name' => 'destroy groups']);

        $role = Role::whereName('admin')
            ->first()
            ->givePermissionTo([
                'index groups',
                'create groups',
                'edit groups',
                'destroy groups'
            ]);
    }
}
