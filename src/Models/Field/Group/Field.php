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

namespace N1ebieski\IDir\Models\Field\Group;

use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\IDir\Models\Field\Field as BaseFieldModel;
use N1ebieski\IDir\Database\Factories\Field\Group\FieldFactory;

/**
 * N1ebieski\IDir\Models\Field\Group\Field
 *
 * @property int $id
 * @property string $model_type
 * @property string $title
 * @property string|null $desc
 * @property \N1ebieski\IDir\ValueObjects\Field\Type $type
 * @property \N1ebieski\IDir\ValueObjects\Field\Visible $visible
 * @property \N1ebieski\IDir\ValueObjects\Field\Options|null $options
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read mixed $decode_value
 * @property-read string $poli
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|Group[] $morphs
 * @property-read int|null $morphs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|BaseFieldModel[] $siblings
 * @property-read int|null $siblings_count
 * @method static \N1ebieski\IDir\Database\Factories\Field\Group\FieldFactory factory(...$parameters)
 * @method static Builder|Field filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Field filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Field filterExcept(?array $except = null)
 * @method static Builder|Field filterGroup(?\N1ebieski\IDir\Models\Group $group = null)
 * @method static Builder|Field filterMorph(?\N1ebieski\IDir\Models\Group $morph = null)
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
 * @mixin \Eloquent
 */
class Field extends BaseFieldModel
{
    // Configuration

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'model_type' => \N1ebieski\IDir\Models\Group::class
    ];

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\IDir\Models\Field\Field::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return FieldFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Field\Group\FieldFactory::new();
    }

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'group';
    }

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute()
    {
        return \N1ebieski\IDir\Models\Group::class;
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        return $this->morphedByMany(\N1ebieski\IDir\Models\Group::class, 'model', 'fields_models');
    }

    // Scopes

    /**
     * [scopeFilterMorph description]
     * @param  Builder $query [description]
     * @param  Group|null  $morph  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterMorph(Builder $query, Group $morph = null): ?Builder
    {
        return $query->when(!is_null($morph), function (Builder $query) use ($morph) {
            return $query->whereHas('morphs', function (Builder $query) use ($morph) {
                return $query->where('model_id', $morph->id);
            });
        });
    }

    // Factories

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
