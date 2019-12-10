<?php

namespace N1ebieski\IDir\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Dir;

/**
 * [DirPolicy description]
 */
class DirPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * [delete description]
     * @param  User $current_user [description]
     * @param  Dir  $dir          [description]
     * @return bool               [description]
     */
    public function delete(User $current_user, Dir $dir) : bool
    {
        return $current_user->id === $dir->user_id;
    }

    /**
     * [edit description]
     * @param  User $current_user [description]
     * @param  Dir  $dir          [description]
     * @return bool               [description]
     */
    public function edit(User $current_user, Dir $dir) : bool
    {
        return $current_user->id === $dir->user_id;
    }
}
