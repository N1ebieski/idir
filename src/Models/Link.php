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
use N1ebieski\IDir\Cache\Link\LinkCache;
use N1ebieski\ICore\Models\Link as BaseLink;
use N1ebieski\IDir\Repositories\Link\LinkRepo;
use N1ebieski\IDir\Database\Factories\Link\LinkFactory;

/**
 * N1ebieski\IDir\Models\Link
 *
 * @property int $id
 * @property \N1ebieski\ICore\ValueObjects\Link\Type $type
 * @property string $url
 * @property string $name
 * @property string|null $img_url
 * @property bool $home
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Franzose\ClosureTable\Extensions\Collection|\N1ebieski\ICore\Models\Category\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read string $created_at_diff
 * @property-read string|null $img_url_from_storage
 * @property-read string $link_as_html
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Collection|BaseLink[] $siblings
 * @property-read int|null $siblings_count
 * @method static \N1ebieski\IDir\Database\Factories\Link\LinkFactory factory(...$parameters)
 * @method static Builder|Link filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Link filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Link filterExcept(?array $except = null)
 * @method static Builder|Link filterOrderBy(?string $orderby = null)
 * @method static Builder|Link filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Link filterReport(?int $report = null)
 * @method static Builder|Link filterSearch(?string $search = null)
 * @method static Builder|Link filterStatus(?int $status = null)
 * @method static Builder|Link filterType(?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link query()
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereHome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUrl($value)
 * @mixin \Eloquent
 */
class Link extends BaseLink
{
    // Configuration

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return BaseLink::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return LinkFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Link\LinkFactory::new();
    }

    // Factories

    /**
     * [makeCache description]
     * @return LinkCache [description]
     */
    public function makeCache()
    {
        return App::make(LinkCache::class, ['link' => $this]);
    }

     /**
     * [makeRepo description]
     * @return LinkRepo [description]
     */
    public function makeRepo()
    {
        return App::make(LinkRepo::class, ['link' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return LinkFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
