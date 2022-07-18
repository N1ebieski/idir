<?php

namespace N1ebieski\IDir\Repositories\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Repositories\User\UserRepo as BaseUserRepo;

class UserRepo extends BaseUserRepo
{
    /**
     * [paginateDirsByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateDirsByFilter(array $filter): LengthAwarePaginator
    {
        /**
         * @var \N1ebieski\IDir\Models\Dir $dir
         */
        $dir = $this->user->dirs()->make();

        return $this->user->dirs()
            ->selectRaw("`{$dir->getTable()}`.*")
            ->withAllPublicRels()
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterStatus($filter['status'])
            ->filterGroup($filter['group'])
            ->when($filter['orderby'] === null, function ($query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getModeratorsByNotificationDirsPermission(): Collection
    {
        return $this->user->permission(['admin.access', 'admin.*'])
            ->permission(['admin.dirs.notification', 'admin.dirs.*', 'admin.*'])
            ->get();
    }
}
