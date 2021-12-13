<?php

namespace N1ebieski\IDir\Models\Traits;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Region\Region;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\Filterable as BaseFilterable;

trait Filterable
{
    use BaseFilterable;

    /**
     * [scopeFilterVisible description]
     * @param  Builder $query  [description]
     * @param  int|null  $visible [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterVisible(Builder $query, int $visible = null): ?Builder
    {
        return $query->when($visible !== null, function ($query) use ($visible) {
            return $query->where('visible', $visible);
        });
    }

    /**
     * [scopeFilterRegion description]
     * @param  Builder       $query  [description]
     * @param  Region|null  $region  [description]
     * @return Builder|null          [description]
     */
    public function scopeFilterRegion(Builder $query, Region $region = null): ?Builder
    {
        return $query->when($region !== null, function ($query) use ($region) {
            return $query->whereHas('regions', function ($query) use ($region) {
                $query->where('id', $region->id);
            });
        });
    }

    /**
     * [scopeFilterGroup description]
     * @param  Builder $query [description]
     * @param  Group|null  $group  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterGroup(Builder $query, Group $group = null): ?Builder
    {
        return $query->when($group !== null, function ($query) use ($group) {
            $query->where('group_id', $group->id);
        });
    }

    /**
     * [scopeFilterType description]
     * @param  Builder $query [description]
     * @param  string|null  $type  [description]
     * @return Builder|null         [description]
     */
    public function scopeFilterType(Builder $query, string $type = null): ?Builder
    {
        return $query->when($type !== null, function ($query) use ($type) {
            return $query->where('type', $type);
        });
    }
}
