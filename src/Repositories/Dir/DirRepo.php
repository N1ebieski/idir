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

namespace N1ebieski\IDir\Repositories\Dir;

use Closure;
use Carbon\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Tag\Dir\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Field\Group\Field;
use N1ebieski\IDir\Models\Rating\Dir\Rating;
use N1ebieski\IDir\Models\Report\Dir\Report;
use N1ebieski\IDir\Models\Comment\Dir\Comment;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\IDir\ValueObjects\Dir\Status as DirStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

class DirRepo
{
    /**
     * [__construct description]
     * @param Dir $dir [description]
     * @param Config   $config   [description]
     * @param Auth $auth
     */
    public function __construct(
        protected Dir $dir,
        protected Config $config,
        protected Auth $auth
    ) {
        //
    }

    /**
     * [paginateByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        return $this->dir->newQuery()
            ->selectRaw("`{$this->dir->getTable()}`.*")
            ->filterAuthor($filter['author'])
            ->filterExcept($filter['except'])
            ->when(!is_null($filter['search']), function (Builder|Dir $query) use ($filter) {
                return $query->filterSearch($filter['search'])
                    ->when($this->auth->user()?->can('admin.dirs.view'), function (Builder $query) {
                        return $query->when(array_key_exists('payment', $this->dir->search), function (Builder $query) {
                            /** @var \N1ebieski\IDir\Models\Payment\Dir\Payment */
                            $payment = $this->dir->payments()->make();

                            $columns = implode(',', $payment->searchable);

                            return $query->leftJoin('payments', function (JoinClause $query) {
                                return $query->on('payments.model_id', '=', 'dirs.id')
                                    ->where([
                                        ['payments.model_type', $this->dir->getMorphClass()],
                                        ['payments.status', PaymentStatus::FINISHED]
                                    ]);
                            })
                            ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [
                                $this->dir->search['payment']
                            ])
                            ->groupBy('dirs.id');
                        })
                        ->when(array_key_exists('user', $this->dir->search), function (Builder $query) {
                            $user = $this->dir->user()->make();

                            $columns = implode(',', $user->searchable);

                            return $query->leftJoin('users', function (JoinClause $query) {
                                return $query->on('users.id', '=', 'dirs.user_id');
                            })
                            ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [
                                $this->dir->search['user']
                            ]);
                        });
                    })
                    ->where(function (Builder $query) {
                        foreach (['id', 'url'] as $attr) {
                            return $query->when(array_key_exists($attr, $this->dir->search), function (Builder $query) use ($attr) {
                                return $query->where("{$this->dir->getTable()}.{$attr}", $this->dir->search[$attr]);
                            });
                        }
                    });
            })
            ->when(
                is_null($filter['status']) && !$this->auth->user()?->can('admin.dirs.view'),
                function (Builder|Dir $query) {
                    return $query->active();
                },
                function (Builder|Dir $query) use ($filter) {
                    return $query->filterStatus($filter['status']);
                }
            )
            ->filterGroup($filter['group'])
            ->filterCategory($filter['category'])
            ->filterReport($filter['report'])
            ->when(is_null($filter['orderby']), function (Builder|Dir $query) use ($filter) {
                $query->filterOrderBySearch($filter['search']);
            })
            ->filterOrderBy($filter['orderby'])
            ->withCount('reports')
            ->withAllRels()
            ->filterPaginate($filter['paginate']);
    }

    /**
     * [paginateForWebByFilter description]
     * @param  array                $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateForWebByFilter(array $filter): LengthAwarePaginator
    {
        // @phpstan-ignore-next-line
        return $this->dir->newQuery()
            ->active()
            ->filterOrderBy($filter['orderby'])
            ->withAllPublicRels()
            ->filterPaginate($this->config->get('database.paginate'));
    }

    /**
     * [countReported description]
     * @return int [description]
     */
    public function countReported(): int
    {
        /** @var Report */
        $report = $this->dir->reports()->make();

        return $report->newQuery()
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
    public function paginateByTagAndFilter(string $tag, array $filter): LengthAwarePaginator
    {
        // @phpstan-ignore-next-line
        return $this->dir->newQuery()
            ->active()
            ->filterOrderBy($filter['orderby'])
            ->withAllTags($tag)
            ->withAllPublicRels()
            ->filterPaginate($this->config->get('database.paginate'));
    }

    /**
     * [paginateBySearch description]
     * @param  string               $name [description]
     * @param  array                $filter  [description]
     * @return LengthAwarePaginator       [description]
     */
    public function paginateBySearchAndFilter(string $name, array $filter): LengthAwarePaginator
    {
        /** @var Tag */
        $tag = $this->dir->tags()->make();

        return $this->dir->newQuery()
            ->selectRaw('`dirs`.*, `privileges`.`name`')
            ->leftJoin('groups_privileges', function (JoinClause $query) {
                return $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->where('privileges.name', 'highest position in search results');
            })
            // Rozbiłem wyszukiwanie na kilka zapytań gdyż chcę wyszukać dirsy
            // po fulltext LUB po ids zawierających okreslony tag,
            // a mysql może wykorzystać tylko 1 indeks
            ->from(
                $this->dir->newQuery()
                    ->selectRaw("`{$this->dir->getTable()}`.*")
                    ->search($name)
                    ->when($tag = $tag->findByName($name), function (Builder $query) use ($tag) {
                        return $query->unionAll(
                            $this->dir->newQuery()
                                ->selectRaw('`dirs`.*, 0 as `url_relevance`, 0 as `title_relevance`, 0 as `content_relevance`')
                                ->join('tags_models', function (JoinClause $query) use ($tag) {
                                    return $query->on('dirs.id', '=', 'tags_models.model_id')
                                        ->where('tags_models.model_type', $this->dir->getMorphClass())
                                        ->where('tags_models.tag_id', $tag->tag_id);
                                })
                                ->groupBy('dirs.id')
                                ->getQuery()
                        );
                    }),
                'dirs'
            )
            ->active()
            ->groupBy('dirs.id')
            ->when(is_null($filter['orderby']), function (Builder|Dir $query) use ($name) {
                return $query->orderBy('privileges.name', 'desc')
                    ->orderBySearch($name)
                    ->latest();
            })
            ->when(!is_null($filter['orderby']), function (Builder|Dir $query) use ($filter, $name) {
                return $query->filterOrderBy($filter['orderby'])
                    ->orderBySearch($name);
            })
            ->withAllPublicRels()
            ->filterPaginate($this->config->get('database.paginate'));
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function getAdvertisingPrivilegedByComponent(array $component): Collection
    {
        return $this->dir->newQuery()
            ->active()
            ->join('groups_privileges', function (JoinClause $query) {
                return $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->where('privileges.name', 'place in the advertising component');
            })
            ->when(!is_null($component['limit']), function (Builder $query) use ($component) {
                return $query->limit($component['limit'])
                    ->inRandomOrder();
            })
            ->withAllPublicRels()
            ->get()
            ->map(function (Dir $dir) use ($component) {
                if ($component['max_content'] !== null) {
                    $dir->content = mb_substr($dir->content, 0, $component['max_content']);
                }

                return $dir;
            });
    }

    /**
     * [firstBySlug description]
     * @param  string $slug [description]
     * @return Dir|null       [description]
     */
    public function firstBySlug(string $slug): ?Dir
    {
        return $this->dir->newQuery()->where('slug', $slug)->first();
    }

    /**
     *
     * @param User $user
     * @return null|Rating
     */
    public function firstRatingByUser(User $user): ?Rating
    {
        return $this->dir->ratings()->where('user_id', $user->id)->first();
    }

    /**
     * [getRelated description]
     * @param  int $limit [description]
     * @return Collection         [description]
     */
    public function getRelated(int $limit = 5): Collection
    {
        return $this->dir->newQuery()
            ->selectRaw('`dirs`.*')
            ->join('categories_models', function (JoinClause $query) {
                return $query->on('dirs.id', '=', 'categories_models.model_id')
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
    public function paginateCommentsByFilter(array $filter): LengthAwarePaginator
    {
        /** @var MorphMany|Comment */
        $comments = $this->dir->comments();

        // @phpstan-ignore-next-line
        return $comments->active()
            ->root()
            ->filterExcept($filter['except'])
            ->filterCommentsOrderBy($filter['orderby'])
            ->withAllRels($filter['orderby'])
            ->filterPaginate($this->config->get('database.paginate'));
    }

    /**
     * Returns latest + privileged dirs collection. Two separated queries, because
     * union has huge performance impact. Other method is order by privilege, but its
     * also very slow (probably by field hasnt index)
     *
     * @return Collection
     */
    public function getLatestForHome(): Collection
    {
        $privileged = $this->dir->newQuery()
            ->selectRaw('`dirs`.*')
            ->join('groups_privileges', function (JoinClause $query) {
                return $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->where('privileges.name', 'highest position on homepage');
            })
            ->active()
            ->latest()
            ->limit($this->config->get('idir.home.max_privileged'))
            ->get();

        $dirs = $this->dir->newQuery()
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
                'group.fields' => function (MorphToMany|Builder|Field $query) {
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
    public function getLatestForModeratorsByLimit(int $limit): Collection
    {
        return $this->dir->newQuery()
            ->whereIn('status', [DirStatus::INACTIVE, DirStatus::ACTIVE])
            ->latest()
            ->limit($limit)
            ->withAllPublicRels()
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param string $timestamp
     * @return Collection
     */
    public function getLatestForModeratorsByCreatedAt(string $timestamp): Collection
    {
        return $this->dir->newQuery()
            ->whereIn('status', [DirStatus::INACTIVE, DirStatus::ACTIVE])
            ->where(function (Builder $query) use ($timestamp) {
                return $query->whereDate('created_at', '>', Carbon::parse($timestamp)->format('Y-m-d'))
                    ->orWhere(function (Builder $query) use ($timestamp) {
                        return $query->whereDate('created_at', '=', Carbon::parse($timestamp)->format('Y-m-d'))
                            ->whereTime('created_at', '>', Carbon::parse($timestamp)->format('H:i:s'));
                    });
            })
            ->latest()
            ->limit(25)
            ->withAllPublicRels()
            ->get();
    }

    /**
     * Undocumented function
     *
     * @param Closure $closure
     * @param string $timestamp
     * @return boolean
     */
    public function chunkAvailableHasPaidRequirementByPrivilegedTo(Closure $closure, string $timestamp): bool
    {
        return $this->dir->newQuery()
            ->active()
            ->whereHas('group', function (BelongsTo $query) {
                return $query->whereHas('prices');
            })
            ->where(function (Builder $query) use ($timestamp) {
                return $query->whereDate(
                    'privileged_to',
                    '<=',
                    Carbon::parse($timestamp)->format('Y-m-d')
                )
                ->orWhere(function (Builder $query) {
                    return $query->whereNull('privileged_at')
                        ->whereNull('privileged_to');
                });
            })
            ->with('user')
            ->chunk(1000, $closure);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getPayments(): Collection
    {
        return $this->dir->payments()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getReportsWithUser(): Collection
    {
        return $this->dir->reports()->with('user')->get();
    }

    /**
     * Undocumented function
     *
     * @param Closure $closure
     * @return boolean
     */
    public function chunkActiveWithModelsCount(Closure $closure): bool
    {
        return $this->dir->newQuery()
            ->active()
            ->withCount(['comments AS models_count' => function (MorphMany|Builder|Comment $query) {
                return $query->root()->active();
            }])
            ->chunk(1000, $closure);
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function getFriendsPrivileged(): Collection
    {
        return $this->dir->newQuery()
            ->active()
            ->join('groups_privileges', function (JoinClause $query) {
                return $query->on('dirs.group_id', '=', 'groups_privileges.group_id')
                    ->join('privileges', 'groups_privileges.privilege_id', '=', 'privileges.id')
                    ->where('privileges.name', 'additional link on the friends subpage');
            })
            ->withAllPublicRels()
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function countByStatus(): Collection
    {
        return $this->dir->newQuery()
            ->selectRaw("`status`, COUNT(`id`) AS `count`")
            ->groupBy('status')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function countByGroup(): Collection
    {
        return $this->dir->newQuery()
            ->selectRaw("`group_id`, COUNT(`id`) AS `count`")
            ->groupBy('group_id')
            ->get();
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getLastActivity(): ?string
    {
        return optional(
            $this->dir->newQuery()->active()->orderBy('updated_at', 'desc')->first('updated_at')
        )
        ->updated_at;
    }

    /**
     * Undocumented function
     *
     * @param array $component
     * @return Collection
     */
    public function getByComponent(array $component): Collection
    {
        return $this->dir->newQuery()
            ->active()
            ->when($component['orderby'] === 'rand', function (Builder $query) {
                return $query->inRandomOrder();
            }, function (Builder|Dir $query) use ($component) {
                return $query->filterOrderBy($component['orderby']);
            })
            ->limit($component['limit'])
            ->withAllPublicRels()
            ->get()
            ->map(function (Dir $dir) use ($component) {
                if ($component['max_content'] !== null) {
                    $dir->content = mb_substr($dir->content, 0, $component['max_content']);
                }

                return $dir;
            });
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public function countByDateAndGroup(): Collection
    {
        /** @var \N1ebieski\IDir\Models\Payment\Dir\Payment */
        $payment = $this->dir->payments()->make();

        /** @var \N1ebieski\IDir\Models\Price */
        $price = $this->dir->group()->make()->prices()->make();

        return $this->dir->newQuery()
            ->selectRaw("YEAR(`d`.`created_at`) `year`, MONTH(`d`.`created_at`) `month`, IFNULL(`p2`.`group_id`, `d`.`group_id`) AS `first_group_id`, COUNT(*) AS `count`")
            ->leftJoin("{$payment->getTable()} AS p1", function (JoinClause $query) {
                return $query->on("p1.model_id", '=', "d.id")
                    ->on(
                        'p1.created_at',
                        '=',
                        DB::raw('(SELECT MIN(`created_at`) FROM `payments` WHERE `model_id` = `p1`.`model_id` AND `model_type` = "' . $this->dir->getMorphClass() . '" AND `status` = ' . PaymentStatus::FINISHED . ')')
                    );
            })
            ->leftJoin("{$price->getTable()} AS p2", function (JoinClause $query) use ($price) {
                return $query->on('p2.id', '=', 'p1.order_id')
                    ->where('p1.order_type', $price->getMorphClass());
            })
            ->from("{$this->dir->getTable()} AS d")
            ->where('d.status', DirStatus::ACTIVE)
            ->groupBy('year')
            ->groupBy('month')
            ->groupBy('first_group_id')
            ->orderBy('year')
            ->orderBy('month')
            ->orderBy('first_group_id')
            ->get();
    }
}
