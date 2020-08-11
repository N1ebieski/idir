<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Config\Repository as Config;
use Carbon\Carbon;
use Closure;
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
        return $this->dir->selectRaw('`dirs`.*')
            ->withCount('reports')
            ->with([
                'group',
                'group.prices',
                'group.fields' => function ($query) {
                    $query->orderBy('position', 'asc');
                },
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
            ->when($filter['search'] !== null, function ($query) use ($filter) {
                $query->filterSearch($filter['search'])
                    ->when(array_key_exists('payment', $this->dir->search), function ($query) {
                        $payment = $this->dir->payments()->make();

                        $columns = implode(',', $payment->searchable);

                        $query->leftJoin('payments', function ($query) use ($payment) {
                            $query->on('payments.model_id', '=', 'dirs.id')
                                ->where([
                                    ['payments.model_type', $this->dir->getMorphClass()],
                                    ['payments.status', $payment::FINISHED]
                                ]);
                        })
                        ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [
                            $this->dir->search['payment']
                        ])
                        ->groupBy('dirs.id');
                    })
                    ->when(array_key_exists('user', $this->dir->search), function ($query) {
                        $user = $this->dir->user()->make();

                        $columns = implode(',', $user->searchable);

                        $query->leftJoin('users', function ($query) {
                            $query->on('users.id', '=', 'dirs.user_id');
                        })
                        ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [
                            $this->dir->search['user']
                        ]);
                    });
            })
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
        return $this->dir->update(['status' => Dir::BACKLINK_INACTIVE]);
    }

    /**
     * [deactivateByStatus description]
     * @return bool [description]
     */
    public function deactivateByStatus() : bool
    {
        return $this->dir->update(['status' => Dir::STATUS_INACTIVE]);
    }

    /**
     * [deactivateByPayment description]
     * @return bool [description]
     */
    public function deactivateByPayment() : bool
    {
        return $this->dir->update(['status' => Dir::PAYMENT_INACTIVE]);
    }

    /**
     * [activate description]
     * @return bool [description]
     */
    public function activate() : bool
    {
        return $this->dir->update(['status' => Dir::ACTIVE]);
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
        return $this->dir->reports()
            ->make()
            ->where('model_type', $this->dir->getMorphClass())
            ->distinct()
            ->count('model_id');
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
        return $this->dir->selectRaw('`dirs`.*, `privileges`.`name`')
            ->withAllPublicRels()
            ->leftJoin('groups_privileges', function ($query) {
                $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->where('privileges.name', 'highest position in search results');
            })
            // Rozbiłem wyszukiwanie na kilka zapytań gdyż chcę wyszukać dirsy
            // po fulltext LUB po ids zawierających okreslony tag,
            // a mysql może wykorzystać tylko 1 indeks    
            ->from(
                $this->dir->search($name)
                    ->when($tag = $this->dir->tags()->make()->findByName($name), function ($query) use ($tag) {
                        $query->unionAll(
                            $this->dir->selectRaw('`dirs`.*')
                                ->join('tags_models', function ($query) use ($tag) {
                                    $query->on('dirs.id', '=', 'tags_models.model_id')
                                        ->where('tags_models.model_type', $this->dir->getMorphClass())
                                        ->where('tags_models.tag_id', $tag->tag_id);
                                })
                                ->groupBy('dirs.id')
                        );
                    }),
                'dirs'
            )
            ->groupBy('dirs.id')
            ->active()
            ->when($filter['orderby'] === null, function ($query) {
                $query->orderBy('privileges.name', 'desc')->latest();
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
            ->join('groups_privileges', function ($query) {
                $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->where('privileges.name', 'place in the advertising component');
            })
            ->withAllPublicRels()
            ->when($component['limit'] !== null, function ($query) use ($component) {
                $query->limit($component['limit'])
                    ->inRandomOrder();
            })
            ->get()
            ->map(function ($item) use ($component) {
                if ($component['max_content'] !== null) {
                    $item->content = mb_substr($item->content, 0, $component['max_content']);
                }

                return $item;
            });
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
        return $this->dir->selectRaw('`dirs`.*')
            ->join('categories_models', function ($query) {
                $query->on('dirs.id', '=', 'categories_models.model_id')
                    ->where('categories_models.model_type', $this->dir->getMorphClass())
                    ->whereIn(
                        'categories_models.category_id',
                        $this->dir->categories->pluck('id')->toArray()
                    );
            })
            ->active()
            ->where('dirs.id', '<>', $this->dir->id)
            ->groupBy('dirs.id')
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
                ['comments.status', \N1ebieski\ICore\Models\Comment\Comment::ACTIVE]
            ])
            ->withAllRels($filter['orderby'])
            ->filterExcept($filter['except'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->filterPaginate($this->paginate);
    }

    /**
     * Returns latest + privileged dirs collection. Two separated queries, because
     * union has huge performance impact. Other method is order by privilege, but its
     * also very slow (probably by field hasnt index)
     *
     * @return Collection
     */
    public function getLatestForHome() : Collection
    {
        $privileged = $this->dir->selectRaw('`dirs`.*')
            ->join('groups_privileges', function ($query) {
                $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->where('privileges.name', 'highest position on homepage');
            })
            ->active()
            ->latest()
            ->limit($this->config->get('idir.home.max_privileged'))
            ->get();

        $dirs = $this->dir
            ->whereNotIn('id', $privileged->pluck('id')->toArray())
            ->active()
            ->latest()
            ->limit($this->config->get('idir.home.max') - $privileged->count())
            ->get();

        return $privileged->merge($dirs)
            ->load([
                'fields',
                'categories',
                'group',
                'group.privileges',
                'group.fields' => function ($query) {
                    return $query->public();
                },
                'tags',
                'regions',
                'ratings'
            ]);
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
            ->whereIn('status', [Dir::INACTIVE, Dir::ACTIVE])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param string $timestamp
     * @return Collection
     */
    public function getLatestForModeratorsByCreatedAt(string $timestamp) : Collection
    {
        return $this->dir->withAllPublicRels()
            ->whereIn('status', [Dir::INACTIVE, Dir::ACTIVE])
            ->where(function ($query) use ($timestamp) {
                $query->whereDate('created_at', '>', Carbon::parse($timestamp)->format('Y-m-d'))
                    ->orWhere(function ($query) use ($timestamp) {
                        $query->whereDate('created_at', '=', Carbon::parse($timestamp)->format('Y-m-d'))
                            ->whereTime('created_at', '>', Carbon::parse($timestamp)->format('H:i:s'));
                    });
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
                $query->whereHas('prices');
            })
            ->where(function ($query) use ($timestamp) {
                $query->whereDate(
                    'privileged_to',
                    '<=',
                    Carbon::parse($timestamp)->format('Y-m-d')
                )
                ->orWhere(function ($query) {
                    $query->whereNull('privileged_at')
                        ->whereNull('privileged_to');
                });
            })
            ->with('user')
            ->chunk(1000, $callback);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getPayments() : Collection
    {
        return $this->dir->payments()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getReportsWithUser() : Collection
    {
        return $this->dir->reports()
            ->with('user')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param Closure $callback
     * @return boolean
     */
    public function chunkActiveWithModelsCount(Closure $callback) : bool
    {
        return $this->dir->active()
            ->withCount(['comments AS models_count' => function ($query) {
                $query->root()->active();
            }])
            ->chunk(1000, $callback);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getFriendsPrivileged() : Collection
    {
        return $this->dir->active()
            ->join('groups_privileges', function ($query) {
                $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->where('privileges.name', 'additional link on the friends subpage');
            })
            ->withAllPublicRels()
            ->get();
    }
}
