<?php

namespace N1ebieski\IDir\Policies;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
     * @param  User $authUser [description]
     * @param  Dir  $dir          [description]
     * @return bool               [description]
     */
    public function delete(User $authUser, Dir $dir): bool
    {
        return $authUser->id === $dir->user_id;
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
}
