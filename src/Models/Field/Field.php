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

namespace N1ebieski\IDir\Models\Field;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\ValueObjects\Field\Options;
use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\IDir\Models\Traits\HasFilterable;
use N1ebieski\IDir\Services\Field\FieldService;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\IDir\Repositories\Field\FieldRepo;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;
use N1ebieski\ICore\Models\Traits\HasPositionable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\IDir\Database\Factories\Field\FieldFactory;

/**
 * N1ebieski\IDir\Models\Field\Field
 *
 * @property Type $type
 * @property Options $options
 * @property Visible $visible
 * @property int $id
 * @property string $model_type
 * @property string $title
 * @property string|null $desc
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \N1ebieski\IDir\Models\Field\Interfaces\RegionsValueInterface&\N1ebieski\IDir\Models\Field\Interfaces\ImageValueInterface&\N1ebieski\IDir\Models\Field\Interfaces\MapValueInterface $morph
 * @property-read string $path
 * @property-read object<value> $pivot
 * @property-read string $created_at_diff
 * @property-read mixed $decode_value
 * @property-read string $poli
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|Field[] $siblings
 * @property-read int|null $siblings_count
 * @method static \N1ebieski\IDir\Database\Factories\Field\FieldFactory factory(...$parameters)
 * @method static Builder|Field filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Field filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Field filterExcept(?array $except = null)
 * @method static Builder|Field filterGroup(?\N1ebieski\IDir\Models\Group $group = null)
 * @method static Builder|Field filterMorph(?\Illuminate\Database\Eloquent\Model $morph = null)
 * @method static Builder|Field filterOrderBy(?string $orderby = null)
 * @method static Builder|Field filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Field filterRegion(?\N1ebieski\IDir\Models\Region\Region $region = null)
 * @method static Builder|Field filterReport(?int $report = null)
 * @method static Builder|Field filterSearch(?string $search = null)
 * @method static Builder|Field filterStatus(?int $status = null)
 * @method static Builder|Field filterType(?string $type = null)
 * @method static Builder|Field filterVisible(?int $visible = null)
 * @method static Builder|Field newModelQuery()
 * @method static Builder|Field newQuery()
 * @method static Builder|Field orderBySearch(string $term)
 * @method static Builder|Field poli()
 * @method static Builder|Field poliType()
 * @method static Builder|Field public()
 * @method static Builder|Field query()
 * @method static Builder|Field search(string $term)
 * @method static Builder|Field whereCreatedAt($value)
 * @method static Builder|Field whereDesc($value)
 * @method static Builder|Field whereId($value)
 * @method static Builder|Field whereModelType($value)
 * @method static Builder|Field whereOptions($value)
 * @method static Builder|Field wherePosition($value)
 * @method static Builder|Field whereTitle($value)
 * @method static Builder|Field whereType($value)
 * @method static Builder|Field whereUpdatedAt($value)
 * @method static Builder|Field whereVisible($value)
 * @method MorphToMany morphs()
 * @mixin \Eloquent
 */
class Field extends Model
{
    use HasPolymorphic;
    use HasCarbonable;
    use HasPositionable;
    use HasFullTextSearchable;
    use HasFilterable;
    use HasFactory;

    // Configuration

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['title'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['title', 'desc', 'type', 'options', 'position', 'visible'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'type' => \N1ebieski\IDir\Casts\Field\TypeCast::class,
        'options' => \N1ebieski\IDir\Casts\Field\OptionsCast::class,
        'visible' => \N1ebieski\IDir\Casts\Field\VisibleCast::class,
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return FieldFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Field\FieldFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function siblings(): HasMany
    {
        return $this->hasMany(\N1ebieski\IDir\Models\Field\Field::class, 'model_type', 'model_type');
    }

    // Scopes

    /**
     * [scopePublic description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visible', Visible::ACTIVE);
    }

    // Accessors

    /**
     * [getDecodeValueAttribute description]
     * @return mixed [description]
     */
    public function getDecodeValueAttribute()
    {
        return json_decode($this->pivot->value);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return FieldRepo [description]
     */
    public function makeRepo()
    {
        return App::make(FieldRepo::class, ['field' => $this]);
    }

    /**
     * [makeService description]
     * @return FieldService [description]
     */
    public function makeService()
    {
        return App::make(FieldService::class, ['field' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return FieldFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
