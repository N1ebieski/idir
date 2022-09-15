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
        $user = new User();

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
