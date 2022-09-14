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

namespace N1ebieski\IDir\Models\Field\Dir;

use N1ebieski\IDir\Models\Field\Field as BaseFieldModel;

/**
 * N1ebieski\IDir\Models\Field\Dir\Field
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
 * @property-read \Illuminate\Database\Eloquent\Collection|BaseFieldModel[] $siblings
 * @property-read int|null $siblings_count
 * @method static \N1ebieski\IDir\Database\Factories\Field\FieldFactory factory(...$parameters)
 * @method static Builder|Field filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Field filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Field filterExcept(?array $except = null)
 * @method static Builder|Field filterGroup(?\N1ebieski\IDir\Models\Group $group = null)
 * @method static Builder|Field filterOrderBy(?string $orderby = null)
 * @method static Builder|Field filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Field filterRegion(?\N1ebieski\IDir\Models\Region\Region $region = null)
 * @method static Builder|Field filterReport(?int $report = null)
 * @method static Builder|Field filterSearch(?string $search = null)
 * @method static Builder|Field filterStatus(?int $status = null)
 * @method static Builder|Field filterType(?string $type = null)
 * @method static Builder|Field filterVisible(?int $visible = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Field newQuery()
 * @method static Builder|Field orderBySearch(string $term)
 * @method static Builder|Field poli()
 * @method static Builder|Field poliType()
 * @method static Builder|Field public()
 * @method static \Illuminate\Database\Eloquent\Builder|Field query()
 * @method static Builder|Field search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Field whereVisible($value)
 * @mixin \Eloquent
 */
class Field extends BaseFieldModel
{
    // Configurations

    /**
     * Undocumented variable
     *
     * @var string
     */
    public $path = 'vendor/idir/dirs/fields';

    // Accessors

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'dir';
    }

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute()
    {
        return \N1ebieski\IDir\Models\Dir::class;
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\IDir\Models\Field\Field::class;
    }
}
