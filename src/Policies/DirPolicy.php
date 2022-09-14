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

namespace N1ebieski\IDir\Policies;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DirPolicy
{
    use HandlesAuthorization;

    /**
     * [view description]
     * @param  User $authUser [description]
     * @param  Dir  $dir          [description]
     * @return bool               [description]
     */
    public function view(User $authUser, Dir $dir): bool
    {
        return $authUser->can('admin.dirs.view')
            || $authUser->id === $dir->user_id;
    }

    /**
     * [edit description]
     * @param  User $authUser [description]
     * @param  Dir  $dir          [description]
     * @return bool               [description]
     */
    public function edit(User $authUser, Dir $dir): bool
    {
        return $authUser->can('admin.dirs.edit')
            || $authUser->id === $dir->user_id;
    }

    /**
     * [delete description]
     * @param  User $authUser [description]
     * @param  Dir  $dir          [description]
     * @return bool               [description]
     */
    public function delete(User $authUser, Dir $dir): bool
    {
        return $authUser->can('admin.dirs.delete')
            || $authUser->id === $dir->user_id;
    }
}
