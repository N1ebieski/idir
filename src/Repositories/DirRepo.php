<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Rating\Dir\Rating;

/**
 * [DirRepo description]
 */
class DirRepo
{
    /**
     * [private description]
     * @var Dir
     */
    protected $dir;

    /**
     * [protected description]
     * @var int
     */
    protected $paginate;

    /**
     * [__construct description]
     * @param Dir $dir [description]
     * @param Config   $config   [description]
     */
    public function __construct(Dir $dir, Config $config)
    {
        $this->dir = $dir;

        $this->config = $config;
        $this->paginate = $config->get('database.paginate');
    }

    /**
     * [paginateForAdminByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateForAdminByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->dir->withCount('reports')
            ->with([
                'group',
                'group.prices',
                'group.fields',
                'group.privileges',
                'fields',
                'regions',
                'categories',
                'tags',
                'user',
                'payments',
                'payments.group'
            ])
            ->withSumRating()
            ->filterAuthor($filter['author'])
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterStatus($filter['status'])
            ->filterGroup($filter['group'])
            ->filterCategory($filter['category'])
            ->filterReport($filter['report'])
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [paginateForWebByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateForWebByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->dir
            ->withAllPublicRels()
            ->active()
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($this->paginate);
    }

    /**
     * [deactivateByBacklink description]
     * @return bool [description]
     */
    public function deactivateByBacklink() : bool
    {
        return $this->dir->update(['status' => 3]);
    }

    /**
     * [deactivateByStatus description]
     * @return bool [description]
     */
    public function deactivateByStatus() : bool
    {
        return $this->dir->update(['status' => 4]);
    }

    /**
     * [activate description]
     * @return bool [description]
     */
    public function activate() : bool
    {
        return $this->dir->update(['status' => 1]);
    }

    /**
     * [nullPrivileged description]
     * @return bool [description]
     */
    public function nullablePrivileged() : bool
    {
        return $this->dir->update([
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    /**
     * [countInactive description]
     * @return int [description]
     */
    public function countInactive() : int
    {
        return $this->dir->inactive()->count();
    }

    /**
     * [countReported description]
     * @return int [description]
     */
    public function countReported() : int
    {
        return $this->dir->whereHas('reports')->count();
    }

    /**
     * [paginateByTag description]
     * @param  string               $tag [description]
     * @param  array                $filter  [description]
     * @return LengthAwarePaginator      [description]
     */
    public function paginateByTagAndFilter(string $tag, array $filter) : LengthAwarePaginator
    {
        return $this->dir->withAllTags($tag)
            ->withAllPublicRels()
            ->active()
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($this->paginate);
    }

    /**
     * [paginateBySearch description]
     * @param  string               $name [description]
     * @param  array                $filter  [description]
     * @return LengthAwarePaginator       [description]
     */
    public function paginateBySearchAndFilter(string $name, array $filter) : LengthAwarePaginator
    {
        $sqlPrivilege = DB::table('privileges')
            ->join('groups_privileges', 'privileges.id', '=', 'groups_privileges.privilege_id')
            ->whereColumn('dirs.group_id', 'groups_privileges.group_id')
            ->whereRaw('`privileges`.`name` = "highest position in search results"')
            ->toSql();

        return $this->dir
            ->selectRaw('`dirs`.*, CASE WHEN EXISTS (' . $sqlPrivilege . ') THEN TRUE ELSE FALSE END AS `privilege`')
            ->withAllPublicRels()
            ->active()
            ->search($name)
            ->when($filter['orderby'] === null, function ($query) {
                $query->orderBy('privilege', 'desc')->latest();
            })
            ->when($filter['orderby'] !== null, function ($query) use ($filter) {
                $query->filterOrderBy($filter['orderby']);
            })
            ->filterPaginate($this->paginate);
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function getAdvertisingPrivilegedByComponent(array $component) : Collection
    {
        return $this->dir->active()
            ->whereHas('group', function ($query) {
                $query->whereHas('privileges', function ($query) {
                    $query->where('name', 'place in the advertising component');
                });
            })
            ->withAllPublicRels()
            ->when($component['limit'] !== null, function ($query) use ($component) {
                $query->limit($component['limit'])
                    ->inRandomOrder();
            })
            ->get();
    }

    /**
     * [firstBySlug description]
     * @param  string $slug [description]
     * @return Category|null       [description]
     */
    public function firstBySlug(string $slug)
    {
        return $this->dir->where('slug', $slug)->first();
    }

    /**
     * [firstRatingByUser description]
     * @param  int    $id [description]
     * @return Rating|null     [description]
     */
    public function firstRatingByUser(int $id) : ?Rating
    {
        return $this->dir->ratings()->where('user_id', $id)->first();
    }
    
    /**
     * [getRelated description]
     * @param  int $limit [description]
     * @return Post|null         [description]
     */
    public function getRelated(int $limit = 5)
    {
        return $this->dir->active()
            ->whereHas('categories', function ($query) {
                $query->whereIn('category_id', $this->dir->categories->pluck('id')->toArray());
            })
            ->where('dirs.id', '<>', $this->dir->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
    
    /**
     * Comments belong to the Dir model
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateCommentsByFilter(array $filter) : LengthAwarePaginator
    {
        return $this->dir->comments()->where([
                ['comments.parent_id', null],
                ['comments.status', 1]
            ])
            ->withAllRels($filter['orderby'])
            ->filterExcept($filter['except'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->filterPaginate($this->paginate);
    }

    /**
     * [getLatest description]
     * @return Collection         [description]
     */
    public function getLatest() : Collection
    {
        $dirs = $this->dir->selectRaw('`dirs`.*, TRUE as `privilege`')
            ->withAllPublicRels()
            ->whereHas('group', function ($query) {
                $query->whereHas('privileges', function ($query) {
                    $query->where('name', 'highest position on homepage');
                });
            })
            ->active()
            ->latest()
            ->limit($this->config->get('idir.home.max_privileged'));

        $sqlPrivilege = DB::table('privileges')
            ->join('groups_privileges', 'privileges.id', '=', 'groups_privileges.privilege_id')
            ->whereColumn('dirs.group_id', 'groups_privileges.group_id')
            ->whereRaw('`privileges`.`name` = "highest position on homepage"')
            ->toSql();

        return $this->dir->selectRaw('`dirs`.*, CASE WHEN EXISTS (' . $sqlPrivilege . ') THEN TRUE ELSE FALSE END AS `privilege`')
            ->withAllPublicRels()
            ->active()
            ->union($dirs)
            ->limit($this->config->get('idir.home.max'))
            ->orderBy('privilege', 'desc')
            ->latest()
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param integer $limit
     * @return Collection
     */
    public function getLatestForModeratorsByLimit(int $limit) : Collection
    {
        return $this->dir->withAllPublicRels()
            ->whereIn('status', [0, 1])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param string $datetime
     * @return Collection
     */
    public function getLatestForModeratorsByCreatedAt(string $datetime) : Collection
    {
        return $this->dir->withAllPublicRels()
            ->whereIn('status', [0, 1])
            ->whereDate('created_at', '>', Carbon::parse($datetime)->format('Y-m-d'))
            ->orWhere(function ($query) use ($datetime) {
                $query->whereDate('created_at', '=', Carbon::parse($datetime)->format('Y-m-d'))
                    ->whereTime('created_at', '>', Carbon::parse($datetime)->format('H:i:s'));
            })
            ->latest()
            ->limit(25)
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @param string $timestamp
     * @return boolean
     */
    public function chunkAvailableHasPaidRequirementByPrivilegedTo(Closure $callback, string $timestamp) : bool
    {
        return $this->dir->active()
            ->whereHas('group', function ($query) {
                $query->whereHas('prices', function ($query) {
                    $query->where('days', '>', 0)
                        ->whereNotNull('days');
                });
            })
            ->where(function ($query) use ($timestamp) {
                $query->whereDate(
                    'privileged_to',
                    '<=',
                    Carbon::parse($timestamp)->format('Y-m-d')
                )
                ->orWhereNull('privileged_to');
            })
            ->chunk(1000, $callback);
    }
}
