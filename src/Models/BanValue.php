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

use Illuminate\Support\Facades\App;
use N1ebieski\IDir\ValueObjects\BanValue\Type;
use N1ebieski\IDir\Cache\BanValue\BanValueCache;
use N1ebieski\ICore\Models\BanValue as BaseBanValue;

/**
 * N1ebieski\IDir\Models\BanValue
 *
 * @property Type $type
 * @property int $id
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $updated_at_diff
 * @method static \N1ebieski\ICore\Database\Factories\BanValue\BanValueFactory factory(...$parameters)
 * @method static Builder|BanValue filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|BanValue filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|BanValue filterExcept(?array $except = null)
 * @method static Builder|BanValue filterOrderBy(?string $orderby = null)
 * @method static Builder|BanValue filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|BanValue filterReport(?int $report = null)
 * @method static Builder|BanValue filterSearch(?string $search = null)
 * @method static Builder|BanValue filterStatus(?int $status = null)
 * @method static Builder|BanValue filterType(?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BanValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BanValue newQuery()
 * @method static Builder|BanValue orderBySearch(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|BanValue query()
 * @method static Builder|BanValue search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|BanValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanValue whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanValue whereValue($value)
 * @mixin \Eloquent
 */
class BanValue extends BaseBanValue
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->casts['type'] = \N1ebieski\IDir\Casts\BanValue\TypeCast::class;

        parent::__construct($attributes);
    }

    // Factories

    /**
     * [makeCache description]
     * @return BanValueCache [description]
     */
    public function makeCache()
    {
        return App::make(BanValueCache::class, ['banvalue' => $this]);
    }
}
