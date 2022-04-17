<?php

namespace N1ebieski\IDir\Http\Responses\Data\Chart\Dir;

use Illuminate\Support\Str;
use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Http\Responses\Data\Chart\DataInterface;

class TimelineData implements DataInterface
{
    /**
     * Undocumented variable
     *
     * @var Group
     */
    protected $group;

    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

    /**
     * Undocumented function
     *
     * @var array
     */
    protected $colors = [
        'rgb(255, 193, 7)',
        'rgb(40, 167, 69)',
        'rgb(23, 162, 184)',
        'rgb(108, 117, 125)',
        'rgb(220, 53, 69)',
        'rgb(0, 123, 255)'
    ];

    /**
     * Undocumented function
     *
     * @param Group $group
     * @param Str $str
     */
    public function __construct(Group $group, Str $str)
    {
        $this->group = $group;

        $this->str = $str;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(Collection $collection): array
    {
        $data = [];

        $groups = $this->group->all()
            ->map(function ($group, $key) {
                $group->color = $this->colors[$key] ?? $this->str->randomColor($group->id);

                return $group;
            });

        $collection->each(function ($item) use (&$data, $groups) {
            $group = $groups->firstWhere('id', $item->first_group_id);

            $data[] = [
                'year' => $item->year,
                'month' => $item->month,
                'group' => [
                    'id' => $item->first_group_id,
                    'name' => optional($group)->name ?? 'Undefined',
                ],
                'count' => $item->count,
                'color' => optional($group)->color ?? $this->str->randomColor('Undefined')
            ];
        });

        return $data;
    }
}
