<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Repositories\UserRepo as BaseUserRepo;

/**
 * [UserRepo description]
 */
class UserRepo extends BaseUserRepo
{
    /**
     * [private description]
     * @var User
     */
    protected $user;

    /**
     * [__construct description]
     * @param User $user [description]
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * [paginateDirsByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateDirsByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->user->dirs()
            ->with(['group', 'categories', 'tags'])
            ->withSumRating()
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterStatus($filter['status'])
            ->filterGroup($filter['group'])
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getModeratorsByNotificationDirsPermission() : Collection
    {
        return $this->user->permission('admin.access')->permission('admin.dirs.notification')->get();
    }
}
