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

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Cache\Dir\DirCache;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\IDir\Models\Field\Dir\Field;
use N1ebieski\IDir\Services\Dir\DirService;
use N1ebieski\IDir\Models\Category\Category;
use N1ebieski\IDir\Repositories\Dir\DirRepo;
use N1ebieski\IDir\Models\Traits\HasFilterable;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use N1ebieski\ICore\Models\Traits\HasStatFilterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use N1ebieski\IDir\Database\Factories\Dir\DirFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\IDir\ValueObjects\Dir\Status as DirStatus;
use N1ebieski\IDir\ValueObjects\Dir\Comment as DirComment;
use N1ebieski\IDir\Models\Field\Interfaces\MapValueInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Models\Field\Interfaces\ImageValueInterface;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;
use N1ebieski\IDir\Models\Field\Interfaces\RegionsValueInterface;

/**
 * N1ebieski\IDir\Models\Dir
 *
 * @property DirStatus $status
 * @property DirComment $comment
 * @property \N1ebieski\IDir\ValueObjects\Dir\Url $url
 * @property \N1ebieski\IDir\Models\Group $group
 * @property \N1ebieski\IDir\Models\Payment\Dir\Payment|null $payment
 * @property int $id
 * @property string $slug
 * @property int $group_id
 * @property int|null $user_id
 * @property string $title
 * @property string $content_html
 * @property string $content
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $privileged_at
 * @property \Illuminate\Support\Carbon|null $privileged_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \N1ebieski\IDir\Models\DirBacklink|null $backlink
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\N1ebieski\IDir\Models\Category\Dir\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\N1ebieski\ICore\Models\Comment\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Field[] $fields
 * @property-read int|null $fields_count
 * @property-read array $attributes_as_array
 * @property-read string $content_as_html
 * @property-read string $created_at_diff
 * @property-read string $less_content_html
 * @property-read string $link_as_html
 * @property-read string $link
 * @property-read string $poli_self
 * @property-read string $privileged_to_diff
 * @property-read string $short_content
 * @property-read string $sum_rating
 * @property-read array $tag_array
 * @property-read array $tag_array_normalized
 * @property-read string $tag_list
 * @property-read string $tag_list_normalized
 * @property-read string $thumbnail_url
 * @property-read string $title_as_link
 * @property-read string $updated_at_diff
 * @property-read string $url_as_link
 * @property-read \N1ebieski\IDir\Models\Map\Map|null $map
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Payment\Dir\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Rating\Dir\Rating[] $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Region\Region[] $regions
 * @property-read int|null $regions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Report\Report[] $reports
 * @property-read int|null $reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Stat\Dir\Stat[] $stats
 * @property-read int|null $stats_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Tag\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \N1ebieski\IDir\Models\User|null $user
 * @method static Builder|Dir active()
 * @method static Builder|Dir activeHasLinkPriviligeByComponent(array $component)
 * @method static Builder|Dir backlinkInactive()
 * @method static \N1ebieski\IDir\Database\Factories\Dir\DirFactory factory(...$parameters)
 * @method static Builder|Dir filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Dir filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Dir filterExcept(?array $except = null)
 * @method static Builder|Dir filterGroup(?\N1ebieski\IDir\Models\Group $group = null)
 * @method static Builder|Dir filterOrderBy(?string $orderby = null)
 * @method static Builder|Dir filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Dir filterRegion(?\N1ebieski\IDir\Models\Region\Region $region = null)
 * @method static Builder|Dir filterReport(?int $report = null)
 * @method static Builder|Dir filterSearch(?string $search = null)
 * @method static Builder|Dir filterStatus(?int $status = null)
 * @method static Builder|Dir filterType(?string $type = null)
 * @method static Builder|Dir filterVisible(?int $visible = null)
 * @method static Builder|Dir findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Dir inactive()
 * @method static Builder|Dir isNotTagged()
 * @method static Builder|Dir isTagged()
 * @method static Builder|Dir newModelQuery()
 * @method static Builder|Dir newQuery()
 * @method static Builder|Dir orderBySearch(string $term)
 * @method static Builder|Dir pending()
 * @method static Builder|Dir query()
 * @method static Builder|Dir search(string $term)
 * @method static Builder|Dir whereContent($value)
 * @method static Builder|Dir whereContentHtml($value)
 * @method static Builder|Dir whereCreatedAt($value)
 * @method static Builder|Dir whereGroupId($value)
 * @method static Builder|Dir whereId($value)
 * @method static Builder|Dir whereNotes($value)
 * @method static Builder|Dir wherePrivilegedAt($value)
 * @method static Builder|Dir wherePrivilegedTo($value)
 * @method static Builder|Dir whereSlug($value)
 * @method static Builder|Dir whereStatus($value)
 * @method static Builder|Dir whereTitle($value)
 * @method static Builder|Dir whereUpdatedAt($value)
 * @method static Builder|Dir whereUrl($value)
 * @method static Builder|Dir whereUserId($value)
 * @method static Builder|Dir withAllPublicRels()
 * @method static Builder|Dir withAllRels()
 * @method static Builder|Dir withAllTags($tags)
 * @method static Builder|Dir withAnyTags($tags)
 * @method static Builder|Dir withCountStats(string $stat)
 * @method static Builder|Dir withSumRating()
 * @method static Builder|Dir withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static Builder|Dir withoutAllTags($tags, bool $includeUntagged = false)
 * @method static Builder|Dir withoutAnyTags($tags, bool $includeUntagged = false)
 * @method Builder|Dir when((\Closure($this): TWhenParameter)|TWhenParameter|null $value, (callable($this, TWhenParameter): TWhenReturnType)|null  $callback, (callable($this, TWhenParameter): TWhenReturnType)|null  $default)
 * @mixin \Eloquent
 */
class Dir extends Model implements
    RegionsValueInterface,
    ImageValueInterface,
    MapValueInterface
{
    use Sluggable;
    use Taggable;
    use HasFullTextSearchable;
    use HasCarbonable;
    use PivotEventTrait;
    use HasFactory;
    use HasFilterable, HasStatFilterable {
        HasStatFilterable::scopeFilterOrderBy insteadof HasFilterable;
    }

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'content',
        'content_html',
        'status',
        'notes',
        'url',
        'privileged_at',
        'privileged_to'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    public $searchable = [
        'url',
        'title',
        'content',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => DirStatus::INACTIVE,
        'notes' => null,
        'privileged_at' => null,
        'privileged_to' => null
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'group_id' => 'integer',
        'user_id' => 'integer',
        'status' => \N1ebieski\IDir\Casts\Dir\StatusCast::class,
        'url' => \N1ebieski\IDir\Casts\Dir\UrlCast::class,
        'privileged_at' => 'datetime',
        'privileged_to' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return DirFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Dir\DirFactory::new();
    }

    // Overrides

    /**
     * Override relacji tags, bo ma hardcodowane nazwy pól
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        $model = Config::get('taggable.model');

        return $this->morphToMany($model, 'model', 'tags_models', 'model_id', 'tag_id')
            ->withTimestamps();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\User::class);
    }

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\Group::class);
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(\N1ebieski\IDir\Models\Payment\Dir\Payment::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphOne
     */
    public function map(): MorphOne
    {
        return $this->morphOne(\N1ebieski\IDir\Models\Map\Map::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function ratings(): MorphMany
    {
        return $this->morphMany(\N1ebieski\IDir\Models\Rating\Dir\Rating::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function regions(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Region\Region::class,
            'model',
            'regions_models',
            'model_id',
            'region_id'
        );
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(\N1ebieski\ICore\Models\Report\Report::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Category\Dir\Category::class,
            'model',
            'categories_models',
            'model_id',
            'category_id'
        );
    }

    /**
     * Undocumented function
     *
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(\N1ebieski\ICore\Models\Comment\Comment::class, 'model');
    }

    /**
     * Undocumented function
     *
     * @return HasOne
     */
    public function backlink(): HasOne
    {
        return $this->hasOne(\N1ebieski\IDir\Models\DirBacklink::class);
    }

    /**
     * Undocumented function
     *
     * @return HasOne
     */
    public function status(): HasOne
    {
        return $this->hasOne(\N1ebieski\IDir\Models\DirStatus::class);
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function fields(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Field\Dir\Field::class,
            'model',
            'fields_values',
            'model_id',
            'field_id'
        )->withPivot('value');
    }

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function stats(): MorphToMany
    {
        return $this->morphToMany(
            \N1ebieski\IDir\Models\Stat\Dir\Stat::class,
            'model',
            'stats_values',
            'model_id',
            'stat_id'
        )->withPivot('value');
    }

    // Scopes

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithAllRels(Builder $query): Builder
    {
        return $query->with([
            'group',
            'group.prices',
            'group.fields' => function (MorphToMany|Builder $query) {
                return $query->orderBy('position', 'asc');
            },
            'group.privileges',
            'fields',
            'regions',
            'categories',
            'tags',
            'user',
            'payments',
            'payments.group',
            'backlink',
            'status'
        ])
        ->when(App::make(MigrationUtil::class)->contains('create_stats_table'), function ($query) {
            $query->with('stats');
        })
        ->withSumRating();
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithAllPublicRels(Builder $query): Builder
    {
        return $query->with([
            'group',
            'group.fields' => function (MorphToMany|Builder $query) {
                return $query->public();
            },
            'group.privileges',
            'fields',
            'regions',
            'categories',
            'tags',
            'user'
        ])
        ->when(App::make(MigrationUtil::class)->contains('create_stats_table'), function ($query) {
            $query->with('stats');
        })
        ->withSumRating();
    }

    /**
     * [scopeWithSumRating description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeWithSumRating(Builder $query): Builder
    {
        return $query->withCount([
            'ratings AS sum_rating' => function (MorphMany|Builder $query) {
                return $query->select(DB::raw('COALESCE(SUM(`ratings`.`rating`)/COUNT(*), 0) as `sum_rating`'));
            }
        ]);
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('dirs.status', DirStatus::ACTIVE);
    }

    /**
     * [scopeInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('dirs.status', DirStatus::INACTIVE);
    }

    /**
     * [scopePending description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('dirs.status', DirStatus::PAYMENT_INACTIVE);
    }

    /**
     * [scopeBacklinkInactive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeBacklinkInactive(Builder $query): Builder
    {
        return $query->where('dirs.status', DirStatus::BACKLINK_INACTIVE);
    }

    /**
     * [scopeActiveHasLinkPriviligeByComponent description]
     * @param  Builder $query     [description]
     * @param  array   $component [description]
     * @return Builder            [description]
     */
    public function scopeActiveHasLinkPriviligeByComponent(Builder $query, array $component): Builder
    {
        return $query->selectRaw('id, url, title AS name, NULL')
            ->whereHas('group', function (Builder $query) {
                return $query->whereHas('privileges', function (Builder $query) {
                    return $query->where('name', 'place in the links component');
                });
            })
            ->join('categories_models', function (JoinClause $query) use ($component) {
                return $query->on('dirs.id', '=', 'categories_models.model_id')
                    ->whereIn('categories_models.category_id', $component['cats'])
                    ->where('categories_models.model_type', $this->getMorphClass());
            })
            ->where('url', '<>', null)
            ->active();
    }

    // Attributes

    /**
     *
     * @return Attribute
     */
    public function poliSelf(): Attribute
    {
        return new Attribute(fn (): string => 'dir');
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function sumRating(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\SumRating::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function thumbnailUrl(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\ThumbnailUrl::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function privilegedToDiff(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\PrivilegedToDiff::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function shortContent(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\ShortContent::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function titleAsLink(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\TitleAsLink::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function urlAsLink(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\UrlAsLink::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function link(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\Link::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function content(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\Content::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function contentHtml(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\ContentHtml::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function contentAsHtml(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\ContentAsHtml::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function lessContentHtml(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\LessContentHtml::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function attributesAsArray(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\AttributesAsArray::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function linkAsHtml(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Dir\LinkAsHtml::class, [
            'dir' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     */
    public function comment(): Attribute
    {
        return new Attribute(fn (): DirComment => DirComment::active());
    }

    // Checkers

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isNulledPrivileges(): bool
    {
        return $this->privileged_at === null && $this->privileged_to === null;
    }

    /**
     * [isRenew description]
     * @return bool [description]
     */
    public function isRenew(): bool
    {
        return ($this->privileged_to !== null || $this->status->isPaymentInactive())
            && $this->getRelation('group')->getRelation('prices')->isNotEmpty();
    }

    /**
     *
     * @param int $id
     * @return bool
     */
    public function isPayment(int $id): bool
    {
        return $this->group_id !== $id || $this->status->isPaymentInactive();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isUrl(): bool
    {
        return !$this->getRelation('group')->url->isInactive() && $this->url->isUrl();
    }

    // Loads

    /**
     * [loadCheckoutPayments description]
     * @return self [description]
     */
    public function loadCheckoutPayments(): self
    {
        return $this->load(['payments' => function (MorphMany|Builder $query) {
            $query->with('orderMorph')->where('status', PaymentStatus::UNFINISHED);
        }]);
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function loadAllPublicRels(): self
    {
        return $this->load(array_filter([
                'fields',
                'categories' => function (MorphToMany|Builder|Category $query) {
                    return $query->withAncestorsExceptSelf();
                },
                'group',
                'group.privileges',
                'group.fields' => function (MorphToMany|Builder|Field $query) {
                    return $query->orderBy('position', 'asc')->public();
                },
                'tags',
                'regions',
                'ratings',
                App::make(MigrationUtil::class)->contains('create_stats_table') ?
                    'stats' : null
            ]));
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function loadAllRels(): self
    {
        return $this->load(array_filter([
            'group',
            'group.privileges',
            'group.fields',
            'fields',
            'regions',
            'ratings',
            'categories' => function (MorphToMany|Builder|Category $query) {
                return $query->withAncestorsExceptSelf();
            },
            'tags',
            'status',
            'backlink',
            App::make(MigrationUtil::class)->contains('create_stats_table') ?
                'stats' : null
        ]));
    }

    // Factories

    /**
     * [makeRepo description]
     * @return DirRepo [description]
     */
    public function makeRepo()
    {
        return App::make(DirRepo::class, ['dir' => $this]);
    }

    /**
     * [makeCache description]
     * @return DirCache [description]
     */
    public function makeCache()
    {
        return App::make(DirCache::class, ['dir' => $this]);
    }

    /**
     * [makeService description]
     * @return DirService [description]
     */
    public function makeService()
    {
        return App::make(DirService::class, ['dir' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return DirFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
