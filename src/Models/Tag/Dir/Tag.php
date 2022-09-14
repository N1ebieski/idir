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

namespace N1ebieski\IDir\Models\Tag\Dir;

use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Tag\Tag as BaseTagModel;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * N1ebieski\IDir\Models\Tag\Dir\Tag
 *
 * @property int $tag_id
 * @property string $name
 * @property string $normalized
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $model_type
 * @property-read string $poli
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Dir[] $morphs
 * @property-read int|null $morphs_count
 * @method static Builder|Tag byName(string $value)
 * @method static \N1ebieski\ICore\Database\Factories\Tag\TagFactory factory(...$parameters)
 * @method static Builder|Tag filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Tag filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Tag filterExcept(?array $except = null)
 * @method static Builder|Tag filterOrderBy(?string $orderby = null)
 * @method static Builder|Tag filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Tag filterReport(?int $report = null)
 * @method static Builder|Tag filterSearch(?string $search = null)
 * @method static Builder|Tag filterStatus(?int $status = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static Builder|Tag orderBySearch(string $term)
 * @method static Builder|Tag poli()
 * @method static Builder|Tag poliType()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static Builder|Tag search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereNormalized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @method static Builder|Tag withCountSum()
 * @mixin \Eloquent
 */
class Tag extends BaseTagModel
{
    // Relations

    /**
     * Undocumented function
     *
     * @return MorphToMany
     */
    public function morphs(): MorphToMany
    {
        $table = Config::get('taggable.tables.taggable_taggables', 'taggable_taggables');

        return $this->morphedByMany(\N1ebieski\IDir\Models\Dir::class, 'taggable', $table, 'tag_id');
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\IDir\Models\Dir::class;
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'dir';
    }
}
