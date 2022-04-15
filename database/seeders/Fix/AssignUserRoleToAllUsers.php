<?php

namespace N1ebieski\IDir\Database\Seeders\Fix;

use Illuminate\Database\Seeder;
use N1ebieski\IDir\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AssignUserRoleToAllUsers extends Seeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        $user = User::make();

        $user->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'user');
        })
        ->chunk(1000, function (Collection $users) {
            $users->each(function (User $user) {
                $user->assignRole('user');
            });
        });
    }
}
