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

namespace N1ebieski\IDir\Models\Traits;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\HasFilterable as BaseHasFilterable;

trait HasFilterable
{
    use BaseHasFilterable;

    /**
     * [scopeFilterVisible description]
     * @param  Builder $query  [description]
     * @param  int|null  $visible [description]
     * @return Builder          [description]
     */
    public function scopeFilterVisible(Builder $query, ?int $visible = null): Builder
    {
        return $query->when(!is_null($visible), function (Builder $query) use ($visible) {
            return $query->where("{$this->getTable()}.visible", $visible);
        });
    }

    /**
     * [scopeFilterRegion description]
     * @param  Builder       $query  [description]
     * @param  Region|null  $region  [description]
     * @return Builder          [description]
     */
    public function scopeFilterRegion(Builder $query, ?Region $region = null): Builder
    {
        return $query->when(!is_null($region), function (Builder $query) use ($region) {
            return $query->whereHas('regions', function (Builder $query) use ($region) {
                // @phpstan-ignore-next-line
                return $query->where('id', $region->id);
            });
        });
    }

    /**
     * [scopeFilterGroup description]
     * @param  Builder $query [description]
     * @param  Group|null  $group  [description]
     * @return Builder         [description]
     */
    public function scopeFilterGroup(Builder $query, ?Group $group = null): Builder
    {
        return $query->when(!is_null($group), function (Builder $query) use ($group) {
            // @phpstan-ignore-next-line
            return $query->where("{$this->getTable()}.group_id", $group->id);
        });
    }

    /**
     * [scopeFilterType description]
     * @param  Builder $query [description]
     * @param  string|null  $type  [description]
     * @return Builder         [description]
     */
    public function scopeFilterType(Builder $query, ?string $type = null): Builder
    {
        return $query->when(!is_null($type), function (Builder $query) use ($type) {
            return $query->where("{$this->getTable()}.type", $type);
        });
    }
}
