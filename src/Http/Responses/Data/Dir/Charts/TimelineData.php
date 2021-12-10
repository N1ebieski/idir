<?php

namespace N1ebieski\IDir\Http\Responses\Data\Dir\Charts;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use N1ebieski\IDir\Http\Responses\Data\DataInterface;
use Illuminate\Contracts\Translation\Translator as Lang;

class TimelineData implements DataInterface
{
    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $collection;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var URL
     */
    protected $url;

    /**
     * Undocumented function
     *
     * @var array
     */
    protected static $colors = [
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
     * @param Collection $collection
     * @param Group $group
     */
    public function __construct(Collection $collection, Group $group)
    {
        $this->collection = $collection;

        $this->group = $group;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        $groups = $this->group->all()
            ->map(function ($group, $key) {
                $group->color = static::$colors[$key] ?? static::getColor($group->id);

                return $group;
            });

        $this->collection->each(function ($item) use (&$data, $groups) {
            $group = $groups->firstWhere('id', $item->first_group_id);

            $data[] = [
                'year' => $item->year,
                'month' => $item->month,
                'group' => [
                    'id' => $item->first_group_id,
                    'name' => optional($group)->name ?? 'Undefined',
                ],
                'count' => $item->count,
                'color' => optional($group)->color ?? static::getColor('Undefined')
            ];
        });
        
        return $data;
    }

    /**
     * Undocumented function
     *
     * @param string $string
     * @return string
     */
    protected static function getColor(string $string): string
    {
        $hash = md5('color' . $string);

        $rgb = [
            hexdec(substr($hash, 0, 2)),
            hexdec(substr($hash, 2, 2)),
            hexdec(substr($hash, 4, 2))
        ];

        return 'rgb(' . implode(',', $rgb) . ')';
    }
}
