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

namespace N1ebieski\IDir\Models\Token;

use N1ebieski\ICore\Models\Token\PersonalAccessToken as BasePersonalAccessToken;

/**
 * N1ebieski\IDir\Models\Token\PersonalAccessToken
 *
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property int|null $symlink_id
 * @property string $name
 * @property string $token
 * @property array|null $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $expired_at_diff
 * @property-read string $updated_at_diff
 * @property-read PersonalAccessToken|null $symlink
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $tokenable
 * @method static Builder|PersonalAccessToken filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|PersonalAccessToken filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|PersonalAccessToken filterExcept(?array $except = null)
 * @method static Builder|PersonalAccessToken filterOrderBy(?string $orderby = null)
 * @method static Builder|PersonalAccessToken filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|PersonalAccessToken filterReport(?int $report = null)
 * @method static Builder|PersonalAccessToken filterSearch(?string $search = null)
 * @method static Builder|PersonalAccessToken filterStatus(?int $status = null)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newQuery()
 * @method static Builder|PersonalAccessToken orderBySearch(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken query()
 * @method static Builder|PersonalAccessToken search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereAbilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereSymlinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PersonalAccessToken extends BasePersonalAccessToken
{
    // Configuration

    /**
     * @var array
     */
    public static $abilities = [
        'api.groups.*',
        'api.groups.view',
        'api.dirs.*',
        'api.dirs.view',
        'api.dirs.create',
        'api.dirs.status',
        'api.dirs.edit',
        'api.dirs.delete'
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        static::$abilities = array_merge(parent::$abilities, static::$abilities);

        parent::__construct($attributes);
    }
}
