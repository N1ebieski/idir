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

namespace N1ebieski\IDir\Repositories\User;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\ICore\Repositories\User\UserRepo as BaseUserRepo;

/**
 * @property User $user
 *
 */
class UserRepo extends BaseUserRepo
{
    /**
     * [paginateDirsByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateDirsByFilter(array $filter): LengthAwarePaginator
    {
        /** @var Dir */
        $dir = $this->user->dirs()->make();

        /** @var HasMany|Dir */
        $dirs = $this->user->dirs();

        // @phpstan-ignore-next-line
        return $dirs->selectRaw("`{$dir->getTable()}`.*")
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterStatus($filter['status'])
            ->filterGroup($filter['group'])
            ->when(is_null($filter['orderby']), function (Builder|Dir $query) use ($filter) {
                return $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->withAllPublicRels()
            ->filterPaginate($filter['paginate']);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getModeratorsByNotificationDirsPermission(): Collection
    {
        return $this->user->newQuery()
            ->permission(['admin.access', 'admin.*'])
            ->permission(['admin.dirs.notification', 'admin.dirs.*', 'admin.*'])
            ->get();
    }
}
