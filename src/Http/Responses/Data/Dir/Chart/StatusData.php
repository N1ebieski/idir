<?php

namespace N1ebieski\IDir\Http\Responses\Data\Dir\Chart;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use N1ebieski\IDir\Http\Responses\Data\DataInterface;
use Illuminate\Contracts\Translation\Translator as Lang;

class StatusData implements DataInterface
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
    protected $colors = [
        Dir::INACTIVE => 'rgb(255, 193, 7)',
        Dir::ACTIVE => 'rgb(40, 167, 69)',
        Dir::PAYMENT_INACTIVE => 'rgb(23, 162, 184)',
        Dir::BACKLINK_INACTIVE => 'rgb(108, 117, 125)',
        Dir::STATUS_INACTIVE => 'rgb(220, 53, 69)',
        Dir::INCORRECT_INACTIVE => 'rgb(0, 123, 255)'
    ];

    /**
     * Undocumented function
     *
     * @param Collection $collection
     * @param Lang $lang
     * @param URL $url
     */
    public function __construct(Collection $collection, Lang $lang, URL $url)
    {
        $this->collection = $collection;

        $this->lang = $lang;
        $this->url = $url;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        $this->collection->sortBy('status')
            ->each(function ($item) use (&$data) {
                $data[] = [
                    'status' => [
                        'value' => $item->status,
                        'label' => $this->lang->get("idir::dirs.status.{$item->status}")
                    ],
                    'count' => $item->count,
                    'color' => $this->colors[$item->status],
                    'links' => [
                        'admin' => $this->url->route('admin.dir.index', ['filter[status]' => $item->status])
                    ]
                ];
            });

        return $data;
    }
}
