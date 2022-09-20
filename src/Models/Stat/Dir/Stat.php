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

namespace N1ebieski\IDir\Models\Stat\Dir;

use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\ICore\Models\Stat\Stat as BaseStatModel;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * N1ebieski\IDir\Models\Stat\Dir\Stat
 *
 * @property int $id
 * @property \N1ebieski\ICore\ValueObjects\Stat\Slug $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read object<value> $pivot
 * @property-read string $created_at_diff
 * @property-read string $model_type
 * @property-read string $poli
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Dir[] $morphs
 * @property-read int|null $morphs_count
 * @method static \Illuminate\Database\Eloquent\Builder|Stat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat poli()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat poliType()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stat whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Stat extends BaseStatModel
{
    // Configuration

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\Stat\Stat::class;
    }

    // Attributes

    /**
     *
     * @return Attribute
     */
    public function poli(): Attribute
    {
        return new Attribute(fn (): string => 'dir');
    }

    /**
     *
     * @return Attribute
     */
    public function modelType(): Attribute
    {
        return new Attribute(fn (): string => \N1ebieski\IDir\Models\Dir::class);
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        return $this->morphedByMany(\N1ebieski\IDir\Models\Dir::class, 'model', 'stats_values');
    }
}
