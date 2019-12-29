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

        Permission::create(['name' => 'index fields']);
        Permission::create(['name' => 'create fields']);
        Permission::create(['name' => 'edit fields']);
        Permission::create(['name' => 'destroy fields']);

        Permission::create(['name' => 'index dirs']);
        Permission::create(['name' => 'create dirs']);
        Permission::create(['name' => 'status dirs']);
        Permission::create(['name' => 'edit dirs']);
        Permission::create(['name' => 'destroy dirs']);

        $role = Role::whereName('admin')
            ->first()
            ->givePermissionTo([
                'index groups',
                'create groups',
                'edit groups',
                'destroy groups',
                'index fields',
                'create fields',
                'edit fields',
                'destroy fields',
                'index dirs',
                'create dirs',
                'status dirs',
                'edit dirs',
                'destroy dirs'
            ]);

        $role = Role::whereName('user')
            ->first()
            ->givePermissionTo([
                'create dirs',
                'edit dirs',
                'destroy dirs'
            ]);
    }
}
