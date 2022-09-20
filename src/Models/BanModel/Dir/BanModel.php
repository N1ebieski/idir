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

namespace N1ebieski\IDir\Models\BanModel\Dir;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use N1ebieski\ICore\Models\BanModel\BanModel as BaseBanModel;

/**
 * N1ebieski\IDir\Models\BanModel\Dir\BanModel
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $poli
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $morph
 * @method static Builder|BanModel filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|BanModel filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|BanModel filterExcept(?array $except = null)
 * @method static Builder|BanModel filterOrderBy(?string $orderby = null)
 * @method static Builder|BanModel filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|BanModel filterReport(?int $report = null)
 * @method static Builder|BanModel filterSearch(?string $search = null)
 * @method static Builder|BanModel filterStatus(?int $status = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel orderBySearch(string $term)
 * @method static Builder|BanModel poli()
 * @method static Builder|BanModel poliType()
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BanModel extends BaseBanModel
{
    use HasFullTextSearchable;

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = ['users.name', 'users.email', 'users.ip'];

    /**
     *
     * @return string
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\ICore\Models\User::class;
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass(): string
    {
        return \N1ebieski\ICore\Models\BanModel\BanModel::class;
    }

    // Accessors

    /**
     *
     * @return Attribute
     */
    public function poli(): Attribute
    {
        return new Attribute(fn (): string => 'user');
    }

    // Repositories

    /**
     * [paginateByFilter description]
     * @param  array  $filter [description]
     * @return LengthAwarePaginator         [description]
     */
    public function paginateByFilter(array $filter): LengthAwarePaginator
    {
        // @phpstan-ignore-next-line
        return $this->newQuery()
            ->select('users.id as id_user', 'users.*', 'bans_models.*', 'bans_models.id as id_ban')
            ->leftJoin('users', function (JoinClause $query) {
                return $query->on('bans_models.model_id', '=', 'users.id')
                    ->where('bans_models.model_type', '=', $this->model_type);
            })
            ->filterExcept($filter['except'])
            ->filterSearch($filter['search'])
            ->filterOrderBy($filter['orderby'])
            ->filterPaginate($filter['paginate']);
    }
}
