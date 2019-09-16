<?php

namespace N1ebieski\IDir\Policies;

use N1ebieski\ICore\Models\User;
use N1ebieski\IDir\Models\Group\Group;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * [GroupPolicy description]
 */
class GroupPolicy
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
     * [actionDefault description]
     * @param  User   $current_user [description]
     * @param  Group   $group         [description]
     * @return bool               [description]
     */
    public function editDefault(User $current_user, Group $group) : bool
    {
        return strtolower($group->name) !== 'default';
    }

    /**
     * [deleteDefault description]
     * @param  User $current_user [description]
     * @param  Group $group         [description]
     * @return bool               [description]
     */
    public function deleteDefault(User $current_user, Group $group) : bool
    {
        return strtolower($group->name) !== 'default';
    }    
}
